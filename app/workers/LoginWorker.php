<?php
namespace App\workers;

use Core\Config;
use Core\Database;
use Core\Session;
use Core\Worker;
use Models\User;

/**
 * Controller Class
 **/
class LoginWorker extends Worker
{
	/* Propierties */
	protected $userTable = "user";

	/* Objects Propierties */
	protected $Database;

	/**
	 * Default Constructor
	 *
	 **/
	public function __construct()
	{
		parent::__construct();
		$this->Database = new Database();
	}

	/**
	 * Returs All users
	 *
	 * @param string $user
	 * @param string $password
	 * @return JSON $response
	 **/
	public function loginUser($user, $password)
	{
		$Config = Config::getInstance();

		$User = new User();
		$User->findByFields([
			'user' => $user
			, 'password' => hash($Config->getValue('config','hash'), $password)
		]);

		if ($User->exists()) {
			$Session = Session::getInstance();
			$Session->setUserData($User);
			$response = ['status' => true, 'msg' => "Success Login"];
		} else {
			$response = ['status' => false,
				'modal' => [
					'title' => $this->trad('modal_title_error')
					, 'msg' => $this->trad('modal_msg_login_fail')
					, 'buttom' => $this->trad('modal_buttom')
				]
			];
		}

		$this->returnJson($response);
	}
}
