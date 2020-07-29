<?php

namespace App\Mail;

use Illuminate\Support\Facades\Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Builder;

class sendCall extends Mailable
{
    use Queueable, SerializesModels;
    
    public $renewals;
    public $validity_date;
    public $instruction_date;
    public $total;
    public $total_ht;
    public $subject;
    public $dest;
    public $step;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($step, $renewals, $validity_date, $instruction_date, $total, $total_ht, $subject, $dest)
    {
        $this->step = $step;
        $this->renewals = $renewals;
        $this->validity_date = $validity_date;
        $this->instruction_date = $instruction_date;
        $this->total = $total;
        $this->total_ht = $total_ht;
        $this->subject = $subject;
        $this->dest = $dest;
        // Added to ask for receipt confirmation
        $this->callbacks[]=(function ($message) {
            $message->getHeaders()->addTextHeader('X-Confirm-Reading-To', '<' . Auth::user()->email . '>');
            $message->getHeaders()->addTextHeader('Return-receipt-to', '<' . Auth::user()->email . '>');
        });
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $templates = \App\TemplateMember::whereHas('class', function (Builder $q) {
            $q->where('name', 'sys_renewals');
        })->where('language', $this->renewals[0]['language'] ?? app()->getLocale());
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
        return $this->view('email.renewalCall', compact('template'));
    }
    
    public function via($notifiable)
    {
        return ['mail'];
    }
}
