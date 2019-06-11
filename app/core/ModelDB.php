<?php
namespace Core;

use Core\Database;

/**
* Database Class
**/
class ModelDB extends Database
{
	/* Constants */
	const R_FIELDS_FROM_INSERT = [ 'created_at' , 'updated_at' , 'deleted_at' ];

	/* Object Properties */
	protected $CurrentObject = null;

	/* Class Properties */

	/* Default Constructor */
	
	/*
	* Set currentObject
	*
	* @param StdClass $object
	**/
	public function setCurrentObject( $object )
	{
		$this->CurrentObject = $object;
	}

	/**
	* Returns reserved fields for insert
	*
	* @return Array
	**/
	public function getReservedFieldsForInsert()
	{
		return self::R_FIELDS_FROM_INSERT;
	}

	/**
	* Manage a operation by transaction
	*
	* @param VascoStatement $operation
	* @return Array
	**/
	protected function manageTransaction( $operation )
	{
		try
		{
			$this->PDO->beginTransaction();
			$operation->execute();
			$this->PDO->commit();

			return $transactionResult = true;
		}
		catch( \Exception $error )
		{
			$this->PDO->rollBack(); 
			$Error = new Error( $error );
			$Error->threatError();
		}
	}

	/**
	* Find data by attribute id
	*
	* @param int $id
	* @return StdClass $object
	**/
	public function _findById( $id )
	{
		$sql = sprintf( 'select * from %s where id = :id' , $this->CurrentObject->getTable() );
		$sth = $this->PDO->prepare( $sql );
		$sth->binds( [ ':id' => $id ] );
		$sth->execute();

		return $sth->fetchObject();
	}

	/**
	* Find data by attribute id
	*
	* @param int $id
	* @return StdClass $object
	**/
	public function _dispense()
	{
		$sql = sprintf( 'describe  %s' , $this->CurrentObject->getTable() );
		$sth = $this->PDO->prepare( $sql );
		$sth->execute();

		$dispenseData = $sth->fetchAll( \PDO::FETCH_COLUMN );

		return $this->_dispenseObject( $dispenseData );
	}

	/**
	* Find data by fields
	*
	* @param Array $dataFields
	* @return StdClass $object
	**/
	public function _findByFields( $dataFields )
	{
		$fields = [];
		$binds = [];
		foreach( $dataFields as $key => $value )
		{
			$fields[] = "`$key` = :$key";
			$binds[ ":$key" ] = $value;
		}

		$sql = sprintf( 'select * from %s where %s' , $this->CurrentObject->getTable() , implode( ' and ' , $fields ) );
		$sth = $this->PDO->prepare( $sql );
		$sth->binds( $binds );
		$sth->execute();

		return $sth->fetchObject();
	}

	/**
	* Returns StdClass object from data by _dispense method
	*
	* @param Array $data
	* @return StdClass $dispenseObject
	**/
	public function _dispenseObject( $data )
	{
		$dispenseObject = new \StdClass();

		foreach( $data as $field ) 
		{
			$dispenseObject->$field = null;
		}

		return $dispenseObject;
	}

	/**
	* Save data to database
	*
	* @return int $lastId
	**/
	public function _save()
	{
		$lastId = null;
		
		if( $this->CurrentObject->isNew() ) 
		{
			$lastId = $this->_insert();
		} 
		else 
		{
			$this->_update();
		}

		return $lastId;
	}

	/**
	* Create data to database
	*
	* @return int $lastId
	**/
	public function _insert()
	{
		$fields = $this->CurrentObject->getFields();
		$insertNameBinds = $this->CurrentObject->getNameBindsFromInsert();
		$binds = $this->CurrentObject->getBindsFromFieldsValues();

		$sql = sprintf( 'insert into %s ( %s ) values( %s )' , $this->CurrentObject->getTable() , implode( ' , ' , $fields ) , implode( ' , ' , $insertNameBinds ) );

		$sth = $this->PDO->prepare( $sql );
		$sth->binds( $binds );

		if( $this->manageTransaction( $sth ) )
			return $this->PDO->lastInsertId(); 
	}

	/**
	* Update data to database
	*
	* @return void
	**/
	public function _update()
	{
		$fields = $this->CurrentObject->getFields( true );

		if( $fields )
		{
			$binds = array_merge( [ ':idx' => $this->CurrentObject->id ] , $this->CurrentObject->getBindsFromFieldsValues( true ) );
	
			$sql = sprintf( 'update %s set %s where id = :idx' , $this->CurrentObject->getTable() , implode( ' , ' , $fields ) );
			$sth = $this->PDO->prepare( $sql );
			$sth->binds( $binds );
			$sth->execute();
		}	
	}

	/**
	* Delete data to dataBase
	*
	* @return void
	**/
	public function _delete()
	{
		$sql = sprintf( 'delete from %s where id = :id' , $this->CurrentObject->getTable() );
		$sth = $this->PDO->prepare( $sql );
		$sth->binds( [ ':id' => $this->CurrentObject->id ] );
		$sth->execute();
	}
}