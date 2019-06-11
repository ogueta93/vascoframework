<?php
namespace App\Workers;

use Core\Config;
use Core\Database;
use Core\Worker;
use Models\User;

/**
 * Controller Class
 **/
class UserWorker extends Worker
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
	 * @param string $nickname
	 * @return StdClass $userData
	 **/
	public function getUserByNickname($nickname)
	{
		$sql = sprintf('select * from %s where user = :nickname', $this->userTable);
		$sth = $this->Database->getPDO()->prepare($sql);
		$sth->binds([':nickname' => $nickname]);
		$sth->execute();

		return $sth->fetchObject();
	}
	
	/**
	 * Returs All users
	 *
	 * @return array $users
	 **/
	public function getAllUsers()
	{
		$sql = sprintf('select * from %s', $this->userTable);
		$sth = $this->Database->getPDO()->prepare($sql);
		$sth->execute();

		return $sth->fetchAll(\PDO::FETCH_OBJ);
	}

	/**
	 * Update users data
	 *
	 * @return $Array users
	 **/
	public function updateUser($data)
	{
		$Config = Config::getInstance();

		$User = new User($data['id']);
		if ($User->getObject()) {
			$User->age = $data['age'] ? $data['age'] : null;

			if ($data['user']) {
				$User->user = $data['user'];
			}

			if ($data['password'] && $User->password != $data['password']) {
				$User->password = hash($Config->getValue('config', 'hash'), $data['password']);
			}

			$User->save();

			$this->returnJson(['status' => true,
				'modal' => [
					'title' => $this->trad('modal_title_updated')
					, 'msg' => $this->trad('modal_msg_updated')
					, 'buttom' => $this->trad('modal_buttom')
				]
			]
			);
		}
	}
}
