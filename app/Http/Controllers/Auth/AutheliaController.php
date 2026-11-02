<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AutheliaController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $settings = $this->settings();
        $state = Str::random(64);
        $verifier = Str::random(64);

        $request->session()->put('authelia', [
            'state' => $state,
            'verifier' => $verifier,
            'expires_at' => time() + 600,
        ]);

        return redirect()->away(rtrim($settings['issuer'], '/').'/api/oidc/authorization?'.http_build_query([
            'client_id' => $settings['client_id'],
            'redirect_uri' => $settings['redirect_uri'],
            'response_type' => 'code',
            'scope' => 'openid profile',
            'state' => $state,
            'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '='),
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986));
    }

    public function callback(Request $request): RedirectResponse
    {
        $settings = $this->settings();
        // Consume the attempt even on failure; a callback can only be used once.
        $attempt = $request->session()->pull('authelia');
        $state = $request->query('state');
        $code = $request->query('code');

        if (! is_array($attempt) || ! is_string($state) || $state === ''
            || ! hash_equals($attempt['state'], $state)
            || $attempt['expires_at'] < time()
            || $request->query('error') !== null || ! is_string($code) || $code === '') {
            return $this->failed();
        }

        $issuer = rtrim($settings['issuer'], '/');

        try {
            // Use client_secret_basic and PKCE. Never follow redirects with credentials.
            $token = Http::asForm()->acceptJson()->timeout(10)->withoutRedirecting()
                ->withBasicAuth($settings['client_id'], $settings['client_secret'])
                ->post($issuer.'/api/oidc/token', [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $settings['redirect_uri'],
                    'code_verifier' => $attempt['verifier'],
                ])->throw();

            $accessToken = $token->json('access_token');
            if (! $token->successful() || ! is_string($accessToken) || $accessToken === '') {
                return $this->failed();
            }

            // Identity comes from Authelia's authenticated UserInfo endpoint.
            // No unverified ID-token claims or browser-supplied identity are used.
            $profile = Http::acceptJson()->timeout(10)->withoutRedirecting()
                ->withToken($accessToken)->get($issuer.'/api/oidc/userinfo')->throw();
        } catch (ConnectionException|RequestException $exception) {
            // Provider responses can contain credentials; do not flash or log them.
            return $this->failed();
        }

        $subject = $profile->json('sub');
        $username = $profile->json('preferred_username');
        if (! $profile->successful() || ! is_string($subject) || $subject === ''
            || ! is_string($username) || $username === '') {
            return $this->failed();
        }

        // Only existing phpIP logins are eligible. Preserve local roles and company scope.
        $user = User::where('login', $username)->first();
        if (! $user || $user->login !== $username) {
            return $this->failed();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/home');
    }

    private function failed(): RedirectResponse
    {
        return redirect()->route('login')->withErrors([
            'authelia' => __('Authelia sign-in failed. Please try again or contact your administrator.'),
        ]);
    }

    private function settings(): array
    {
        abort_unless(config('services.authelia.enabled'), 404);
        $settings = config('services.authelia');
        foreach (['issuer', 'client_id', 'client_secret', 'redirect_uri'] as $key) {
            abort_unless(is_string($settings[$key] ?? null) && trim($settings[$key]) !== '', 503);
        }
        foreach (['issuer', 'redirect_uri'] as $key) {
            $url = parse_url($settings[$key]);
            abort_unless(filter_var($settings[$key], FILTER_VALIDATE_URL)
                && ($url['scheme'] ?? '') === 'https' && ! empty($url['host'])
                && ! isset($url['user']) && ! isset($url['pass'])
                && ! isset($url['query']) && ! isset($url['fragment']), 503);
        }

        return $settings;
    }
}
