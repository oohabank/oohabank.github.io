<?php

namespace App\Models\Mail;

use App\Bootstrap;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Send {
	private $mail, $error;

	private $smtp = true;

	private $subject = '<No subject>';

	private $body = 'Qexy!';

	private $altbody = 'Qexy!';

	private $address = [];

	public function __construct(?string $subject = null, ?string $body = null, ?string $address = ''){
		$this->mail = new PHPMailer();

		$config = Bootstrap::getConfig();

		$this->setSMTP($config['mail']['smtp']);

		if(!is_null($subject)){
			$this->setSubject($subject);
		}

		if(!is_null($body)){
			$this->setBody($body);
		}

		if(!is_null($address)){
			$this->addAddress($address);
		}
	}

	public function getMailer() : ?PHPMailer {
		return $this->mail;
	}

	public function setSMTP(bool $value = true) : self {
		$this->smtp = $value;

		return $this;
	}

	public function getSMTP() : bool {
		return $this->smtp;
	}

	public function getError() : ?string {
		return $this->error;
	}

	public function setBody(string $html) : self {
		$this->body = $html;

		$this->setAltBody(strip_tags($html));

		return $this;
	}

	public function getBody() : string {
		return $this->body;
	}

	public function setAltBody(string $text) : self {
		$this->altbody = $text;

		return $this;
	}

	public function getAltBody() : string {
		return $this->altbody;
	}

	public function setSubject(string $text) : self {
		$this->subject = $text;

		return $this;
	}

	public function getSubject() : string {
		return $this->subject;
	}

	public function addAddress(string $mail, string $name = '') : self {

		$this->address[] = [$mail, $name];

		return $this;
	}

	public function execute() : bool {
		$mail = $this->getMailer();

		$config = Bootstrap::getConfig();

		try {
			$mail->CharSet = 'UTF-8';

			$mail->isHTML(true);

			$mail->setFrom($config['mail']['from'], $config['mail']['from_name']);

			$mail->addReplyTo($config['mail']['from'], 'No Reply');

			if($this->getSMTP()){
				$mail->isSMTP();
				$mail->SMTPAuth = true;
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
				$mail->Port = $config['mail']['port'];
				$mail->Host = $config['mail']['host'];
				$mail->Username = $config['mail']['username'];
				$mail->Password = $config['mail']['password'];
			}else{
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->SMTPAuth = false;
				$mail->Port = 587;
			}

			$mail->Body = $this->getBody();

			$mail->AltBody = $this->getAltBody();

			$mail->Subject = $this->getSubject();

			foreach($this->address as $ar){
				$mail->addAddress($ar[0], $ar[1]);
			}

			$mail->send();

		}catch(Exception $e){
			$this->error = $e->getMessage();
		}

		return is_null($this->error);
	}
}

?>