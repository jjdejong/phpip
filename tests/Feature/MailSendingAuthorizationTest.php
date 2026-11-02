<?php

namespace Tests\Feature;

use App\Http\Controllers\RenewalController;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailSendingAuthorizationTest extends TestCase
{
    public function test_guests_cannot_trigger_renewal_emails(): void
    {
        Mail::fake();
        foreach (['/renewal/call/1', '/renewal/reminder', '/renewal/lastcall'] as $path) {
            $this->post($path, ['task_ids' => [1]])->assertRedirect('/login');
        }
        Mail::assertNothingSent();
    }

    public function test_clients_and_read_only_staff_cannot_trigger_renewal_emails(): void
    {
        Mail::fake();
        foreach (['CLI', 'CNT', 'DBRO', null] as $role) {
            $this->be((new User)->forceFill(['id' => 100, 'login' => 'mail-test', 'default_role' => $role]));
            foreach (['/renewal/call/1', '/renewal/reminder', '/renewal/lastcall'] as $path) {
                $this->post($path, ['task_ids' => [1]])->assertForbidden();
            }
        }
        Mail::assertNothingSent();
    }

    public function test_staff_with_write_permission_can_reach_the_existing_sending_actions(): void
    {
        $controller = \Mockery::mock(RenewalController::class)->makePartial();
        foreach (['firstcall', 'remindercall', 'lastcall'] as $action) {
            $controller->shouldReceive($action)->twice()->andReturn(response()->noContent());
        }
        $this->app->instance(RenewalController::class, $controller);
        foreach (['DBA', 'DBRW'] as $role) {
            $this->be((new User)->forceFill(['id' => 100, 'login' => 'mail-test', 'default_role' => $role]));
            foreach (['/renewal/call/1', '/renewal/reminder', '/renewal/lastcall'] as $path) {
                $this->post($path, ['task_ids' => [1]])->assertNoContent();
            }
        }
    }
}
