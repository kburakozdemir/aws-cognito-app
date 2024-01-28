<?php

/**
 * @file
 */

require 'bootstrap.php';

use AWSCognitoApp\AWSCognitoWrapper;

session_start();

$wrapper = new AWSCognitoWrapper();
$wrapper->initialize();

$wrapper->logout();

$_SESSION = array();

session_destroy();

header('Location: /');

exit();
