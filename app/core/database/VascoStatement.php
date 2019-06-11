<?php
namespace Core\database;

/**
* VascoStatement Class
**/
class VascoStatement extends \PDOStatement 
{
	/* Constants */

	/* Object Properties */

	/* Class Properties */
	public $dbh;

	/* Default Constructor */
	protected function __construct( $dbh ) 
	{
		$this->dbh = $dbh;
	}

	/**
	* Set binds from current query
	*
	* @param Array $binds
	* @param Array $typeArray
	**/
	public function binds( $binds , $typeArray = null )
	{
		foreach( $binds as $key => $value )
		{
			if( $typeArray )
				$this->bindValue( $key , $value , $typeArray[ $key ] );
			else
			{
				if( is_int( $value ) )
					$param = \PDO::PARAM_INT;
				elseif( is_bool( $value ) )
					$param = \PDO::PARAM_BOOL;
				elseif( is_null( $value ) )
					$param = \PDO::PARAM_NULL;
				elseif( is_string( $value ) )
					$param = \PDO::PARAM_STR;
				else
					$param = false;

				$this->bindValue( $key , $value , $param );
			}
		}
	}
}