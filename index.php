<?php
use Core\App;

require_once "vendor/autoload.php";
require_once "app/core/functions/functions.php";
require_once "app/core/constants/constants.php";

$App = App::getInstance();
$App->startApp();
