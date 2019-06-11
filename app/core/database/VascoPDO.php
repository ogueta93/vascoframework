<?php
namespace Core\database;

/**
* VascoPDO Class
**/
class VascoPDO extends \PDO
{
	/* Constants */
	const CUSTOM_STATEMENT = 'Core\database\VascoStatement';

	/* Object Properties */

	/* Class Properties */

	/* Default Constructor */
	public function __construct( $dsn , $username = "" , $password = "" , $driver_options = [] ) 
	{
		parent::__construct( $dsn , $username , $password , $driver_options );
		$this->setAttribute( \PDO::ATTR_STATEMENT_CLASS , [ self::CUSTOM_STATEMENT , [ $this ] ] );
		$this->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->setAttribute( \PDO::ATTR_EMULATE_PREPARES , true );
		$this->setAttribute( \PDO::ATTR_ORACLE_NULLS , \PDO::NULL_TO_STRING );
	}
}