<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $verification;

    public function __construct($data)
    {
        $this->verification = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('welcome')
                ->with([
                    'code' => $this->verification['code'],
                    'email' => $this->verification['email'],
                    'image' => asset('storage/logo1.png'),
                ]);
    }
}
