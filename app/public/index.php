<?php

/**
 * @file
 */

require 'bootstrap.php';

use AWSCognitoApp\AWSCognitoWrapper;

$wrapper = new AWSCognitoWrapper();
$wrapper->initialize();

if (isset($_POST['action'])) {

  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  if ($_POST['action'] === 'register') {
    $email = $_POST['email'] ?? '';
    $error = $wrapper->signup($username, $email, $password);

    if (empty($error)) {
      header('Location: confirm.php?username=' . $username);
      exit;
    }
  }

  if ($_POST['action'] === 'login') {
    $error = $wrapper->authenticate($username, $password);

    if (empty($error)) {
      header('Location: secure.php');
      exit;
    }
  }
}

$message = '';
if (isset($_GET['reset'])) {
  $message = 'Your password has been reset. You can now login with your new password';
}
?>

<!doctype html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='x-ua-compatible' content='ie=edge'>
        <title>AWS Cognito App - Register and Login</title>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
    </head>
    <body>
        <?php include "inc/menu.php"; ?>

        <?php
        if (isset($error)) {
          ?>
        <p style='color: red;'><?php echo $error;?></p>
          <?php
        }
        ?>
        <p style='color: green;'><?php echo $message;?></p>
        <h1>Register</h1>
        <form method='post' action=''>
            <input type='text' placeholder='Username' name='username' /><br />
            <input type='text' placeholder='Email' name='email' /><br />
            <input type='password' placeholder='Password' name='password' /><br />
            <input type='hidden' name='action' value='register' />
            <input type='submit' value='Register' />
        </form>

        <h1>Login</h1>
        <form method='post' action=''>
            <input type='text' placeholder='Username' name='username' /><br />
            <input type='password' placeholder='Password' name='password' /><br />
            <input type='hidden' name='action' value='login' />
            <input type='submit' value='Login' />
        </form>
        <p><a href='/forgotpassword.php'>Forgot password?</a></p>
    </body>
</html>
