<?php
namespace Core;

use Core\Config;
use Core\session\Tokenaizer;
use Core\Singleton;

/**
 * Session class
 **/
class Session extends Singleton
{
	/* Constants */
	const SESSION_STARTED = true;
	const SESSION_NOT_STARTED = false;

	const VASCO_SESSION = 'vSession';

	const APP = 'app';
	const APP_LANGUAGE = 'language';
	const APP_TOKEN = 'token';

	const USER = 'user';

	const SESS_ID = 'sessId';
	const USER_TOKEN = 'loginToken';

	/* Properties */
	protected $sessionState = self::SESSION_NOT_STARTED;
	protected $data;

	/* Objects Propierties */
	protected $Config = null;

	protected function __construct()
	{
		$this->Config = Config::getInstance();

		$this->startSession();
	}

	/**
	 * (Re)starts the session.
	 *
	 * @return bool TRUE if the session has been initialized, else FALSE.
	 **/
	public function startSession()
	{
		if ($this->sessionState == self::SESSION_NOT_STARTED) {
			$sessionConfig = $this->Config->getRawValue('config', 'sessionConfig');
			$this->resumeSessionId();
			$this->sessionState = session_start($sessionConfig);
			$this->initSession();
		}

		return $this->sessionState;
	}

	/**
	 * Serializes the data and save in $_SESSION
	 *
	 * @return void
	 */
	protected function setData()
	{
		$_SESSION[self::VASCO_SESSION] = serialize($this->data);
	}

	/**
	 * Unserializes the data and set data
	 *
	 * @return void
	 */
	protected function getData()
	{
		$this->data = isset($_SESSION[self::VASCO_SESSION]) ? unserialize($_SESSION[self::VASCO_SESSION]) : [];
	}

	/**
	 * Unsets session variable
	 *
	 * @param string $key
	 * @return void
	 */
	function unset($key) {
		if (isset($this->data[$key])) {
			unset($this->data[$key]);
		}
		$this->setData();
	}

	/**
	 * Sets session value
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param bool $merge
	 */
	public function set($key, $data, $merge = true)
	{
		if (isset($this->data[$key]) && is_array($data)) {
			$this->data[$key] = $merge ? array_replace_recursive($this->data[$key], $data) : $data;
		} else {
			$this->data[$key] = $data;
		}
		$this->setData();
	}

	/**
	 * Gets session value
	 *
	 * @param string $key
	 * @return mixed $data
	 */
	public function get($key)
	{
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	/**
	 * Sets Language on the session
	 *
	 * @param string $language
	 * @return void
	 */
	public function setLanguage($language = null)
	{
		$data = [
			self::APP_LANGUAGE => $language ?? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)
		];

		$this->set(self::APP, $data);
	}

	/**
	 * Gets language
	 *
	 * @return mixed $language
	 */
	public function getLanguage()
	{
		return isset($this->data[self::APP][self::APP_LANGUAGE]) ? $this->data[self::APP][self::APP_LANGUAGE] : null;
	}

	/** 
	 * Sets sessId on the session
	 * 
	 * @return void
	*/
	public function setSessId()
	{
		$data = [
			self::SESS_ID => bin2hex(session_id())
		];

		$this->set(self::APP, $data);
	}

	/**
	 * Gets sessId
	 * 
	 * @return mixed $sessId
	 */
	public function getSessId()
	{
		return isset($this->data[self::APP][self::SESS_ID]) ? hex2bin($this->data[self::APP][self::SESS_ID]) : null;
	}

	/**
	 * Unsets Token
	 *
	 * @param string $name
	 * @return void
	 */
	public function unsetToken($name)
	{
		unset($this->data[self::APP][self::APP_TOKEN][$name]);
		$this->setData();
	}

	/**
	 * Sets Token
	 *
	 * @param string name
	 * @param int $timeLimit
	 */
	public function setToken($name, $timeLimit = null)
	{
		$data = [
			self::APP_TOKEN => [$name => new Tokenaizer($name, true, $timeLimit)]
		];

		$this->set(self::APP, $data);
	}

	/**
	 * Gets token
	 *
	 * @param string $name
	 * @return mixed $Token
	 */
	public function getToken($name)
	{
		$Token = isset($this->data[self::APP][self::APP_TOKEN][$name]) ? $this->data[self::APP][self::APP_TOKEN][$name] : null;

		return $Token;
	}

	/**
	 * Sets UserData on the session
	 *
	 * @param object $UserData
	 * @return void
	 */
	public function setUserData($UserData)
	{
		$this->set(self::USER, $UserData->getObject(), false);
		$this->setToken(self::USER_TOKEN, (int) $this->Config->getValue('config', 'user_token_expired_time'));
	}

	/**
	 * Gets User data
	 *
	 * @return object $UserData or null
	 */
	public function getUserData()
	{
		$UserData = null;
		if ($UserData = $this->get(self::USER)) {
			$UserData->userToken = $this->getToken(self::USER_TOKEN)->getValue();
		}

		return $UserData;
	}

	/**
	 * Destroys the current session.
	 *
	 * @return bool TRUE is session has been deleted, else FALSE.
	 **/
	public function destroy()
	{
		if ($this->sessionState == self::SESSION_STARTED) {
			$this->sessionState = !session_destroy();
			$this->data = null;
			unset($_SESSION);

			return !$this->sessionState;
		}

		return false;
	}

	/**
	 * Resume session by id in REQUEST params
	 *
	 * @return void
	 */
	protected function resumeSessionId()
	{
		$sessIdKey = sprintf('__%s' , self::SESS_ID);
		if (isset($_REQUEST[$sessIdKey])) {
			session_id($_REQUEST[$sessIdKey]);
			unset($_REQUEST[$sessIdKey]);
		}
	}

	/**
	 * Initializes session values
	 *
	 * @return void
	 */
	protected function initSession()
	{
		$this->getData();

		if (!isset($this->data[self::APP])) {
			$this->data[self::APP] = [
				self::APP_TOKEN => []
			];

			$this->setSessId();
			$this->setLanguage('en');
		} else {
			$this->refresh();
		}

		$this->setData();
	}

	/**
	 * Refresh session tokens
	 *
	 * @return void
	 */
	protected function refresh()
	{
		/* session user */
		if ($this->get(self::USER)) {
			$UserToken = $this->getToken(self::USER_TOKEN);
			if ($UserToken->hasExpired()) {
				$this->unset(self::USER);
				$this->unsetToken(self::USER_TOKEN);
			} else {
				$UserToken->regenerateToken();
			}
		}
	}
}
