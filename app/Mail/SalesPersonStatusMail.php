<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalesPersonStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $status;

    public function __construct(User $user, string $status)
    {
        $this->user = $user;
        $this->status = $status;
    }

    public function build()
    {
        $subject = $this->status === 'approved'
            ? 'Your Sales Person Account Has Been Approved'
            : 'Your Sales Person Account Registration Was Not Approved';

        return $this->subject($subject)->view('emails.sales_person_status');
    }
}