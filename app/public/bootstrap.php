<?php

/**
 * @file
 */

require '../vendor/autoload.php';

use Dotenv\Dotenv;

/**
 * Include phpDotEnv to manage environment variables.
 */
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
