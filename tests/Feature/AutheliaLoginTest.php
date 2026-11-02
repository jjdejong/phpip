<?php

namespace Tests\Feature;

use App\Models\Actor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AutheliaLoginTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.authelia' => [
            'enabled' => true,
            'issuer' => 'https://auth.example.com',
            'client_id' => 'phpip',
            'client_secret' => 'test-secret',
            'redirect_uri' => 'https://phpip.example.com/login/authelia/callback',
        ]]);
        Http::preventStrayRequests();
    }

    private function begin(): array
    {
        $response = $this->get(route('authelia.redirect'))->assertRedirect();
        parse_str(parse_url($response->headers->get('Location'), PHP_URL_QUERY), $parameters);

        return $parameters;
    }

    private function provider(array $profile): void
    {
        Http::fake([
            'https://auth.example.com/api/oidc/token' => Http::response(['access_token' => 'test-token']),
            'https://auth.example.com/api/oidc/userinfo' => Http::response($profile),
        ]);
    }

    private function localUser(): User
    {
        $actor = Actor::create(['name' => 'Authelia test', 'login' => 'authelia-test']);

        return User::findOrFail($actor->id);
    }

    public function test_disabled_option_is_hidden_and_routes_reject_requests(): void
    {
        config(['services.authelia.enabled' => false]);
        $this->get('/login')->assertOk()->assertDontSee(route('authelia.redirect'));
        $this->get('/login/authelia')->assertNotFound();
        $this->get('/login/authelia/callback')->assertNotFound();
        Http::assertNothingSent();
    }

    public function test_enabled_option_keeps_password_login_available(): void
    {
        $this->get('/login')->assertOk()->assertSee(__('Sign in with Authelia'))
            ->assertSee('name="password"', false);
    }

    public function test_redirect_contains_session_bound_state_and_pkce(): void
    {
        $parameters = $this->begin();
        $attempt = session('authelia');
        $this->assertSame('phpip', $parameters['client_id']);
        $this->assertSame('code', $parameters['response_type']);
        $this->assertSame('openid profile', $parameters['scope']);
        $this->assertSame($attempt['state'], $parameters['state']);
        $this->assertSame('S256', $parameters['code_challenge_method']);
        $this->assertSame(rtrim(strtr(base64_encode(hash('sha256', $attempt['verifier'], true)), '+/', '-_'), '='), $parameters['code_challenge']);
        Http::assertNothingSent();
    }

    public function test_existing_account_is_authenticated_with_its_local_permissions(): void
    {
        $user = $this->localUser();
        $role = $user->default_role;
        $parameters = $this->begin();
        $verifier = session('authelia.verifier');
        $this->provider(['sub' => 'authelia-subject', 'preferred_username' => $user->login, 'groups' => ['DBA']]);

        $this->withSession(['url.intended' => '/matter'])->get('/login/authelia/callback?'.http_build_query([
            'state' => $parameters['state'], 'code' => 'test-code',
        ]))->assertRedirect('/matter')->assertSessionMissing('authelia');
        $this->assertAuthenticatedAs($user);
        $this->assertSame($role, $user->fresh()->default_role);
        Http::assertSent(fn (Request $request) => $request->url() === 'https://auth.example.com/api/oidc/token'
            && $request['code_verifier'] === $verifier && $request['code'] === 'test-code'
            && $request['redirect_uri'] === config('services.authelia.redirect_uri')
            && $request->hasHeader('Authorization', 'Basic '.base64_encode('phpip:test-secret')));
        Http::assertSent(fn (Request $request) => $request->url() === 'https://auth.example.com/api/oidc/userinfo'
            && $request->hasHeader('Authorization', 'Bearer test-token'));
    }

    public function test_callback_without_a_login_attempt_is_rejected(): void
    {
        $this->get('/login/authelia/callback?state=forged&code=code')->assertRedirect('/login')->assertSessionHasErrors('authelia');
        $this->assertGuest();
        Http::assertNothingSent();
    }

    public function test_wrong_state_consumes_attempt_without_contacting_provider(): void
    {
        $this->begin();
        $this->get('/login/authelia/callback?state=forged&code=code')->assertRedirect('/login')
            ->assertSessionHasErrors('authelia')->assertSessionMissing('authelia');
        $this->assertGuest();
        Http::assertNothingSent();
    }

    public function test_expired_attempt_is_rejected(): void
    {
        $parameters = $this->begin();
        session()->put('authelia.expires_at', time() - 1);
        $this->get('/login/authelia/callback?state='.$parameters['state'].'&code=code')->assertRedirect('/login')->assertSessionHasErrors('authelia');
        Http::assertNothingSent();
        $this->assertGuest();
    }

    public function test_provider_denial_is_handled(): void
    {
        $parameters = $this->begin();
        $this->get('/login/authelia/callback?state='.$parameters['state'].'&error=access_denied')
            ->assertRedirect('/login')->assertSessionHasErrors('authelia');
        Http::assertNothingSent();
        $this->assertGuest();
    }

    public function test_unknown_username_does_not_create_an_account(): void
    {
        $count = User::count();
        $parameters = $this->begin();
        $this->provider(['sub' => 'new-subject', 'preferred_username' => 'unprovisioned-authelia-user']);
        $this->get('/login/authelia/callback?state='.$parameters['state'].'&code=code')
            ->assertRedirect('/login')->assertSessionHasErrors('authelia');
        $this->assertGuest();
        $this->assertSame($count, User::count());
    }

    public function test_profile_without_username_cannot_fall_back_to_email(): void
    {
        $parameters = $this->begin();
        $this->provider(['sub' => 'subject', 'email' => $this->localUser()->email]);
        $this->get('/login/authelia/callback?state='.$parameters['state'].'&code=code')
            ->assertRedirect('/login')->assertSessionHasErrors('authelia');
        $this->assertGuest();
    }

    public function test_provider_error_does_not_leak_response_or_allow_replay(): void
    {
        $parameters = $this->begin();
        Http::fake(['*' => Http::response(['error' => 'sensitive-provider-details'], 401)]);
        $url = '/login/authelia/callback?state='.$parameters['state'].'&code=code';
        $this->get($url)->assertRedirect('/login')->assertSessionHasErrors('authelia');
        $this->get($url)->assertRedirect('/login')->assertSessionHasErrors('authelia');
        Http::assertSentCount(1);
        $this->get('/login')->assertDontSee('sensitive-provider-details');
        $this->assertGuest();
    }

    public function test_missing_configuration_and_insecure_issuer_fail_closed(): void
    {
        config(['services.authelia.client_secret' => '']);
        $this->get('/login/authelia')->assertStatus(503);
        config(['services.authelia.client_secret' => 'secret', 'services.authelia.issuer' => 'http://auth.example.com']);
        $this->get('/login/authelia')->assertStatus(503);
        Http::assertNothingSent();
    }

    public function test_provider_connection_failure_returns_to_login(): void
    {
        $parameters = $this->begin();
        Http::fake(['*' => Http::failedConnection()]);
        $this->get('/login/authelia/callback?state='.$parameters['state'].'&code=code')
            ->assertRedirect('/login')->assertSessionHasErrors('authelia');
        $this->assertGuest();
    }

    public function test_token_redirect_is_rejected_without_requesting_userinfo(): void
    {
        $parameters = $this->begin();
        Http::fake(['*' => Http::response('', 302, ['Location' => 'https://other.example.com'])]);
        $this->get('/login/authelia/callback?state='.$parameters['state'].'&code=code')
            ->assertRedirect('/login')->assertSessionHasErrors('authelia');
        Http::assertSentCount(1);
        $this->assertGuest();
    }

    public function test_case_mismatch_does_not_link_an_account(): void
    {
        $user = $this->localUser();
        $parameters = $this->begin();
        $this->provider(['sub' => 'subject', 'preferred_username' => strtoupper($user->login)]);
        $this->get('/login/authelia/callback?state='.$parameters['state'].'&code=code')
            ->assertRedirect('/login')->assertSessionHasErrors('authelia');
        $this->assertGuest();
    }

    public function test_local_password_login_and_logout_still_work(): void
    {
        $user = $this->localUser();
        $user->password = 'local-test-password';
        $user->save();
        $this->post('/login', ['login' => $user->login, 'password' => 'local-test-password'])->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
        Http::assertNothingSent();
    }
}
