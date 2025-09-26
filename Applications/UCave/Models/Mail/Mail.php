<?php

namespace App\Models\Mail;

class Mail {

	public static function Send(?string $subject = null, ?string $body = null, ?string $address = '') : Send {
		return new Send($subject, $body, $address);
	}
}

?>