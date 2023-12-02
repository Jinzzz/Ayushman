<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class PatientEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $phone;
    public $password;
    public $subject = 'Your Registration with Ayushman has been successful!';

    /**
     * Create a new message instance.
     *
     * @param string $phone
     * @param string $password
     * @return void
     */
    public function __construct($phone, $password)
    {
        $this->phone = $phone;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->view('emails.patient-emails');
    }
}
