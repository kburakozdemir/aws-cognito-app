<?php

/**
 * @file
 */

require 'bootstrap.php';

use AWSCognitoApp\AWSCognitoWrapper;

$wrapper = new AWSCognitoWrapper();
$wrapper->initialize();

$wrapper->logout();

header('Location: /');
