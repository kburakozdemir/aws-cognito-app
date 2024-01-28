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


  $result = $wrapper->adminGetUser($username);

  if ($result["UserStatus"] == "CONFIRMED") {
    $messageToPrint = "User has already been confirmed";
    $url = "/?messagetoprint=" . $messageToPrint;
    header('Location: ' . $url);
    exit;
  }
  else {
    if ($result["UserStatus"] == "UNCONFIRMED") {
      $error = $wrapper->resendConfirmationCode($username);
    }
  }

  if ($result["UserStatus"] == "N/A") {
    $error = "User does not exist";
  }

  if (empty($error)) {
    header('Location: confirm.php');
  }
}

$username = $_GET['username'] ?? '';
?>

<!doctype html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='x-ua-compatible' content='ie=edge'>
        <title>AWS Cognito App - Resend Confirmation Code</title>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <?php echo file_get_contents('inc/head.html'); ?>
    </head>
    <body>
        <?php echo file_get_contents('inc/menu.html'); ?>

        <?php
        if (isset($error)) {
          ?>
        <p style='color: red;'><?php echo $error;?></p>
          <?php
        }
        ?>
        <h2>Resend Confirmation Code</h2>
        <form method='post' action=''>
            <input type='text' placeholder='Username' name='username' value='<?php echo $username;?>' /><br />
            <input type='hidden' name='action' value='resendconfirmation' />
            <input type='submit' value='Confirm' />
        </form>
    </body>
</html>
