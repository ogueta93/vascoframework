<?php
namespace Core;

use Core\ModelDB;

/**
 * Model class
 **/
class Model
{
	/* Object Properties */
	protected $ModelDB;
	protected $Object;
	protected $_Object;

	/* Class Properties */
	protected $table;
	protected $isNew = true;
	protected $notNull = false;

	/* Default Constructor */
	public function __construct($id = null)
	{
		$this->ModelDB = new ModelDB();
		$this->ModelDB->setCurrentObject($this);
		if ($id) {
			$this->findById($id);
		} else {
			$this->dispense();
		}
	}

	public function __destruct()
	{
		$this->ModelDB->closeConnection();
	}

	public function __get($key)
	{
		return $this->Object->$key;
	}

	public function __set($key, $value)
	{
		$this->Object->$key = $value;
	}

	/**
	 * Check if field value has change before include to update
	 *
	 * @param string $key
	 * @return bool $result
	 **/
	protected function _checkChangeField($key)
	{
		$result = false;

		if ($this->_Object && isset($this->_Object->$key)) {
			$result = $this->Object->$key != $this->_Object->$key ? true : false;
		}

		return $result;
	}

	/**
	 * Set new variables
	 *
	 * @param StdClass $data
	 * @return void
	 **/
	protected function setNew($data)
	{
		$this->isNew = true;
		$this->notNull = true;
		$this->setInitialObject($data);
		$this->Object = $data;
	}

	/**
	 * Set finded variables
	 *
	 * @param StdClass $data
	 * @return void
	 **/
	protected function setFinded($data)
	{
		$this->isNew = false;
		$this->notNull = true;
		$this->setInitialObject($data);
		$this->Object = $data;
	}

	/**
	 * Set created variables
	 *
	 * @param int $id
	 * @return void
	 **/
	protected function setCreated($id)
	{
		$this->id = $id;
		$this->isNew = false;
		$this->notNull = true;
		$this->setInitialObject($this->Object);
	}

	/** Set deleted Object
	 *
	 * @return void
	 **/
	protected function setDeleted()
	{
		$this->isNew = true;
		$this->notNull = false;
		$this->Object = null;
		$this->_Object = null;
	}

	/** Set notFinded Object
	 *
	 * @return void
	 **/
	protected function setNotFinded()
	{
		$this->isNew = true;
		$this->notNull = false;
		$this->Object = null;
		$this->_Object = null;
	}

	/**
	 * Set initial object
	 *
	 * @param StdClass $data
	 * @return void
	 **/
	protected function setInitialObject($data)
	{
		$this->_Object = clone ($data);
	}

	/**
	 * Get object
	 *
	 * @return $object
	 **/
	public function getObject()
	{
		return $this->Object;
	}

	/**
	 * Get table name
	 *
	 * @return string $table
	 **/
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * Return property isNew
	 *
	 * @return bool $isNew
	 **/
	public function isNew()
	{
		return $this->isNew;
	}

	/**
	 * Return property notNull
	 *
	 * @return bool $notNull
	 **/
	public function exists()
	{
		return $this->notNull;
	}

	/**
	 * Get Object fields
	 *
	 * @param bool $toUpdate
	 * @return Array $fields
	 **/
	public function getFields($toUpdate = false)
	{
		$fields = [];

		foreach ($this->Object as $key => $value) {
			if ($toUpdate) {
				if ($this->_checkChangeField($key)) {
					$fields[] = "`$key` = :$key";
				}

			} else {
				if (!in_array($key, $this->ModelDB->getReservedFieldsForInsert())) {
					$fields[] = "`$key`";
				}

			}
		}

		return $fields;
	}

	/**
	 * Get name binds from insert
	 *
	 * @return Array $nameBinds
	 **/
	public function getNameBindsFromInsert()
	{
		$nameBinds = [];

		foreach ($this->Object as $key => $value) {
			if (!in_array($key, $this->ModelDB->getReservedFieldsForInsert())) {
				$nameBinds[] = ":$key";
			}

		}

		return $nameBinds;
	}

	/**
	 * Get Binds from Fields
	 *
	 * @param bool $toUpdate
	 * @return Array $binds
	 **/
	public function getBindsFromFieldsValues($toUpdate = false)
	{
		$binds = [];

		foreach ($this->Object as $key => $value) {
			if ($toUpdate) {
				if ($this->_checkChangeField($key)) {
					$binds[":$key"] = $value;
				}

			} else {
				if (!in_array($key, $this->ModelDB->getReservedFieldsForInsert())) {
					$binds[":$key"] = $value;
				}

			}
		}

		return $binds;
	}

	/**
	 * Find data by attribute id
	 *
	 * @param int $id
	 **/
	public function findById($id)
	{
		$data = $this->ModelDB->_findById($id);
		if ($data) {
			$this->setFinded($data);
		} else {
			$this->setNotFinded();
		}

	}

	/**
	 * Dispense a blank Object structure from mysql
	 *
	 * @return void
	 **/
	public function dispense()
	{
		$data = $this->ModelDB->_dispense();
		if ($data) {
			$this->setInitialObject($data);
			$this->Object = $data;
		}
	}

	/**
	 * Find data by fields
	 *
	 * @param Array $dataFields
	 **/
	public function findByFields($dataFields)
	{
		$data = $this->ModelDB->_findByFields($dataFields);
		if ($data) {
			$this->setFinded($data);
		} else {
			$this->setNotFinded();
		}

	}

	/**
	 * Save data to database
	 *
	 * @return int $id
	 **/
	public function save()
	{
		$id = $this->ModelDB->_save();
		if ($id) {
			$this->setCreated($id);
		}

		return $id;
	}

	/**
	 * Deleted data permanently o updated deleted_at
	 *
	 * @param bool $permanently
	 * @return void
	 **/
	public function delete($permanently = false)
	{
		if ($permanently) {
			$this->ModelDB->_delete();
			$this->setDeleted();
		} else {
			$this->deleted_at = date("Y-m-d H:i:s");
			$this->ModelDB->_save();
		}
	}

	/**
	 * Hydrate a Model by StdClass data
	 *
	 * @param StdClass $object
	 * @param bool $new
	 * @return void
	 **/
	public function hydrate($object, $new = false)
	{
		foreach ($this->Object as $originalKey => $OriginalValue) {
			foreach ($object as $key => $value) {
				if ($originalKey == $key) {
					$this->Object->$originalKey = $value;
				}

			}
		}

		if ($new) {
			$this->setNew($this->Object);
		} else {
			$this->setFinded($this->Object);
		}
	}
}
