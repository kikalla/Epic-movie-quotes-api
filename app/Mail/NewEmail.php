<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewEmail extends Mailable
{
	use Queueable, SerializesModels;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($data)
	{
		$this->username = array_values($data)[0];
		$this->token = array_values($data)[1];
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		$url = config('movie-quotes.app-url') . '/email/verify/' . $this->token;
		return $this->from(env('MAIL_USERNAME'))
		->subject('Welcome')
		->view('new-email', ['url' => $url, 'username' => $this->username]);
	}
}
