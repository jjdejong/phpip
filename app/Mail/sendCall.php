<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class sendCall extends Mailable
{
    use Queueable, SerializesModels;

    public $renewals;
    protected $language;

    public function __construct(public $step, $renewals, public $validity_date, public $instruction_date, public $total, public $total_ht, public $subject, public $dest)
    {
        $this->renewals = collect($renewals)->sortBy([
            ['caseref', 'asc'],
            ['country', 'asc']
        ])->values();
        
        $this->language = $this->renewals->first()['language'] ?? app()->getLocale();

        // Added to ask for receipt confirmation
        $this->callbacks[] = (function ($message) {
            $message->getHeaders()->addTextHeader('X-Confirm-Reading-To', '<'.Auth::user()->email.'>');
            $message->getHeaders()->addTextHeader('Return-receipt-to', '<'.Auth::user()->email.'>');
        });
    }

    public function build()
    {
        $templates = \App\Models\TemplateMember::whereHas('class', function (Builder $q) {
            $q->where('name', 'sys_renewals');
        })->where('language', $this->language);
        if ($this->step == 'first') {
            $template = $templates->where('category', 'firstcall');
        }
        if ($this->step == 'last') {
            $template = $templates->where('category', 'lastcall');
        }
        if ($this->step == 'warn') {
            $template = $templates->where('category', 'warncall');
        }
        // Fails with code 404 if no template found
        $template = $template->firstOrFail();
        $this->subject .= $template->subject;

        return $this->view('email.renewalCall', [
            'template' => $template,
            'language' => $this->language,
            'renewals' => $this->renewals
        ]);
    }

    public function via($notifiable)
    {
        return ['mail'];
    }
}
