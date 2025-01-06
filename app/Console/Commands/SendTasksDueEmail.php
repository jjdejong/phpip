<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTasksDueEmail extends Command
{
    protected $signature = 'tasks:send-due-email';
    protected $description = 'Send email for tasks due in the next 30 days';

    public function handle()
    {
        $tasks = Task::query()
        ->whereHas('matter', function ($query) {
            $query->where('dead', 0);
        })
            ->where('code', '!=', 'REN')
            ->where('due_date', '<', now()->addDays(30))
            ->where('done', 0)
            ->with('matter', 'info')
            ->orderBy('due_date')
            ->get();

        // Send email
        Mail::html(
            view('email.tasks-due', [
                'tasks' => $tasks,
                'phpip_url' => config('tasks-email.phpip_url') . '/matter',
            ])->render(),
            function ($message) {
                $message
                    ->from(config('tasks-email.email_from'))
                    ->to(config('tasks-email.email_to'))
                    ->bcc(config('tasks-email.email_bcc'))
                    ->subject('[phpIP] - Tasks due in the next 30 days');
            }
        );
    }
}
