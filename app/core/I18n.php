<?php
namespace Core;

use Core\Helpers\DataTransform;
use Core\Singleton;

/**
 * I18n Class
 **/
class I18n extends Singleton
{
	/* Constants */
	const DEFAULT_LANGUAGE = 'en';

	/* Properties */
	protected $rute = DIR . 'app/i18n/';
	protected $languague = 'null';
	protected $book = null;

	/* Default Constructor */
	public function __construct()
	{
		//Get languague from Session
		$Session = Session::getInstance();
		$this->language = $Session->getLanguage() ? $Session->getLanguage() : self::DEFAULT_LANGUAGE;

		//Localizatting I18n Book
		if (file_exists($this->rute . $this->language . '.php')) {
			$book = include $this->rute . $this->language . '.php';
		} else {
			$book = include $this->rute . self::DEFAULT_LANGUAGE . '.php';
		}

		//Transform book to stdClass
		$DataTransform = new DataTransform($book);
		$this->book = $DataTransform->toStdClass();
	}

	/**
	 * Gets single traduction for string
	 *
	 * @param String $key
	 * @return String $result
	 **/
	public function getTrad($key)
	{
		$result = $key;

		if (property_exists($this->book, $key)) {
			$result = $this->book->$key;
		}

		return $result;
	}

	/**
	 * Gets traductions for Array
	 *
	 * @param Array $arrayToTrad
	 * @return Array $arrayTranslated
	 **/
	public function getArrayTrad($arrayToTrad)
	{
		$arrayTranslated = array_map([$this, 'getTrad'], $arrayToTrad);

		return $arrayTranslated;
	}
}
