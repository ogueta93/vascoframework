<?php
namespace Core\Helpers;

use Core\Config;
use PHPMailer;

/**
 * Email Helper class
 **/
class Email
{
	/* Object Properties */
	protected $PHPMailer = null;
	protected $Config = null;

	/* Default Constructor */
	public function __construct()
	{
		$this->PHPMailer = new PHPMailer();
		$this->Config = Config::getInstance();
	}

	/**
	 * Send a Email with the config params
	 *
	 * @param string $subject
	 * @param string $body
	 * @param array $to 
	 * @return boolean $result
	 **/
	public function send($subject, $body, $to)
	{
		$result = false;

		//$this->PHPMailer->SMTPDebug = 3;
		$this->PHPMailer->CharSet = 'UTF-8';
		$this->PHPMailer->isSMTP();
		$this->PHPMailer->Host = $this->Config->getValue('email', 'host');
		$this->PHPMailer->SMTPAuth = true;
		$this->PHPMailer->Username = $this->Config->getValue('email', 'username');
		$this->PHPMailer->Password = $this->Config->getValue('email', 'password');
		$this->PHPMailer->SMTPSecure = $this->Config->getValue('email', 'secure');
		$this->PHPMailer->Port = $this->Config->getValue('email', 'port');
		$this->PHPMailer->isHTML(true);

		$this->PHPMailer->Subject = $subject;
		$this->PHPMailer->Body = $body;

		$this->PHPMailer->setFrom($this->Config->getValue('email', 'contact'), $this->Config->getValue('email', 'contactName'));
		foreach ($to as $key => $value) {
			$this->PHPMailer->addAddress($value);
		}

		if ($this->PHPMailer->send()) {
			$result = true;
		}

		return $result;
	}
}
