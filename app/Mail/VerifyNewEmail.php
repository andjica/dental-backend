<?php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyNewEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $verifyUrl = url('/api/verify-new-email/' . $this->user->email_verification_token);

        return $this->subject('Verify your new email address')
                    ->markdown('emails.verify-new-email')
                    ->with([
                        'verifyUrl' => $verifyUrl,
                        'user' => $this->user,
                    ]);
    }
}
