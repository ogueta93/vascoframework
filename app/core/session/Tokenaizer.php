<?php
namespace Core\session;

use Core\Config;

/**
 * Tokenaizer Class
 **/
class Tokenaizer
{
	/* Constants */

	/* Object Properties */
	protected $name = null;
	protected $timeLimit = null;
	protected $token = null;

	/* Class Properties */
	protected $Config = null;

	/**
	 *  Default Constructor
	 *
	 * @param string $name
	 * @param bool $generateToken
	 */
	public function __construct($name, $generateToken = false, $timeLimit = null)
	{
		$this->Config = Config::getInstance();

		$this->name = $name;
		$this->timeLimit = $timeLimit;

		if ($generateToken) {
			$this->createToken();
		}
		if (is_null($timeLimit)) {
			$this->timeLimit = $this->Config->getValue('config', 'token_expired_time');
		}
	}

	/**
	 * Gets token value
	 *
	 * @return string token
	 */
	public function getValue()
	{
		if (!$this->token) {
			$this->createToken();
		}

		return $this->token;
	}

	/**
	 * Regenerates token
	 *
	 * @return void
	 */
	public function regenerateToken()
	{
		$this->createToken();
	}

	/**
	 * Validates a token
	 *
	 * @param string $token
	 * @return bool $result
	 */
	public function isValid($token)
	{
		$result = false;

		if ($this->token && $this->token == $token) {
			$result = !$this->hasExpired();
		}

		return $result;
	}

	/**
	 * Checks if the token has expired
	 *
	 * @return bool $result
	 */
	public function hasExpired()
	{
		$result = true;

		$decodeToken = hex2bin($this->token);
		$tokenParts = explode('-date-', $decodeToken);
		$datetime1 = \DateTime::createFromFormat('Y-m-d H:i:s', $tokenParts[1]);
		$datetime2 = new \DateTime();

		if ($this->getTimeLimit() > 0 && ($datetime2->getTimestamp() - $datetime1->getTimestamp()) < $this->getTimeLimit()) {
			$result = false;
		}

		return $result;
	}

	/**
	 * Returns token time limit in seconds
	 *
	 * @return int $timeLimit
	 */
	protected function getTimeLimit()
	{
		return (int) $this->timeLimit;
	}

	/**
	 * Creates a valid token
	 *
	 * @return void
	 */
	protected function createToken()
	{
		$now = date('Y-m-d H:i:s');
		$tokenString = sprintf('%s%s-date-%s', $this->name, random_bytes($this->Config->getValue('config', 'token_bytes')), $now);
		$this->token = bin2hex($tokenString);
	}
}
