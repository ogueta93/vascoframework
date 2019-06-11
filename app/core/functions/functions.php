<?php

use Core\Session;
use Core\I18n;

/**
 * Location of Defined Global Functions
 **/

/**
 * Get App's Dir( Internal Files )
 *
 * @return string $finalDir
 **/
function _getDIR()
{
	$dir = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos( $_SERVER['SCRIPT_FILENAME'], '/'));
	$finalDir = "$dir/";

	return $finalDir;
}

/**
 * Gets App's Url(Resources and Others)
 *
 * @return string $url
 **/
function _getURL()
{
	$url = sprintf(
		"%s://%s/",
		isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
		$_SERVER['HTTP_HOST']
	);

	return $url;
}

/**
 * Get App's Resources Dir
 *
 * @return string $dir
 **/
function _getResources()
{
	return _getURL() . "resources/";
}

/**
 * Redirects to specific url
 *
 * @param string $url 
 * @param bool $permanent 
 **/
function _redirect($url, $permanent = false)
{
	header('Location: ' . $url, true, $permanent ? 301 : 302);
}

/**
 * Include a micro view on a View
 *
 * @param string $view
 **/
function _microView($view)
{
	include _getDIR() . 'app/views/' . $view . '.php';
}

/**
 * Gets translation by key 
 *
 * @param string $key
 * @return string $result
 **/
function _trad($key)
{
	$I18n = I18n::getInstance();

	return $I18n->getTrad($key);
}

/**
 * Returns noticies likes Exceptions
 *
 * @param string $severity
 * @param string $message
 * @param string $filename
 * @param string $lineno
 *
 * @return ErrorException
 **/
function exceptions_error_handler($severity, $message, $filename, $lineno)
{
	if (error_reporting() == 0) {
		return;
	}
	if (error_reporting() & $severity) {
		throw new ErrorException($message, 0, $severity, $filename, $lineno);
	}
}
