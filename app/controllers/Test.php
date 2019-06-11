<?php

namespace Controllers;

use App\workers\LoginWorker;
use App\workers\UserWorker;
use Core\Config;
use Core\Controller;
use Core\Session;
use Models\User;

/**
 * Default Controller
 **/
class Test extends Controller
{
	/* Propierties */
	protected $view = 'defaultView';

	/**
	 * Default method
	 **/
	public function init()
	{
		$this->View->returnView();
	}

	public function login()
	{
		$this->View->setView('login');
		$this->View->returnView();
	}

	public function router()
	{
		$this->View->setView('vueRouter');
		$this->View->returnView();
	}

	public function users()
	{
		$this->View->setView('users');
		$this->View->returnView();
	}

	public function loginUser($user, $password)
	{
		$LoginWorker = new LoginWorker();
		$LoginWorker->loginUser($user, $password);
	}

	public function start($id)
	{
		$User = new User($id);

		if ($User->getObject()) {
			$Session = Session::getInstance();
			$Session->setUserData($User);
			$this->Response->returnJson(['status' => true, 'msg' => "Success Login"]);
		}
	}

	public function resume($vue = null)
	{
		$Session = Session::getInstance();
		$data = ['status' => true, 'msg' => "Success UserData", 'data' => $Session->getUserData()];
		$this->Response->returnJson($data);
	}

	public function getAllUsers()
	{
		$UserWorker = new UserWorker();
		$allUsers = $UserWorker->getAllUsers();
	}

	public function updateUser($user)
	{
		$UserWorker = new UserWorker();
		$allUsers = $UserWorker->updateUser($user);
	}

	public function createUser($user, $pass)
	{
		$Config = Config::getInstance();

		$User = new User();
		$User->user = $user;
		$User->password = hash($Config->getValue('config', 'hash'), $pass);
		$User->save();
	}

	public function test($id = null)
	{
		$UserWorker = new UserWorker();
		$userData = $UserWorker->getUserByNickname('pepe2');
		$User = new User();
		$User->hydrate($userData);
		print_r($User->user);
	}

	public function session()
	{
		$Session = Session::getInstance();
		var_dump($Session);
	}

	public function SessionId()
	{
		$Session = Session::getInstance();
		$sessId = $Session->getSessId();
		var_dump($sessId);
	}

	public function destroySession()
	{
		$Session = Session::getInstance();
		$Session->destroy();
	}
}
