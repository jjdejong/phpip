<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->step == 'first') {
            return $this->view('email.firstcall');
        }
        elseif ($this->step == 'last') {
            return $this->view('email.lastcall');
        }
        elseif ($this->step == 'warn') {
            return $this->view('email.warncall');
        }
    }
    
    
    public function via($notifiable)
    {
        return ['mail'];
    }
}
