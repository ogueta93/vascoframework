<?php
use Core\Session;
use Core\Response;

/**
 * Location of Middleware's functions
 *
 * @return $result bool or void
 **/

/* Demo function */
function demo()
{
	return true;
}

/* Demo function false */
function demoFalse()
{
	return false;
}

/* Checks if the user is logged */
function checkLogged($vue = null)
{
	$Session = Session::getInstance();

	$result = $Session->getUserData();
	if (!$result) {
		if ($vue) {
			echo json_encode([]);
		} else {
			_redirect('/');
		}

	} else {
		return true;
	}
}

/* Checks if the user is a Guest */
function checkGuest($vue = null)
{
	$Session = Session::getInstance();

	$result = $Session->getUserData();
	if (!$result) {
		return true;
	} else {
		if ($vue) {
			echo json_encode([]);
		} else {
			_redirect('/');
		}

	}
}

/* Check if protocol POST is used */
function checkPost()
{
	$result = $_SERVER['REQUEST_METHOD'] !== 'GET' ? true : false;

	return $result;
}
