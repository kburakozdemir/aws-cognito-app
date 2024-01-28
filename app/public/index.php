<?php

/**
 * @file
 */

require 'bootstrap.php';

use AWSCognitoApp\AWSCognitoWrapper;

session_start();

$wrapper = new AWSCognitoWrapper();
$wrapper->initialize();

if (isset($_GET['messagetoprint'])) {
    $messageToPrint = $_GET['messagetoprint'];
}

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
            $_SESSION['status'] = "logged_in";
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
        <?php echo file_get_contents('inc/head.html'); ?>
    </head>
    <body class="<?php echo (isset($_SESSION['status'])) ? $_SESSION['status'] : '' ?>">
      <?php echo file_get_contents('inc/menu.html'); ?>
      <div class="container mt-5">
        <?php
        if (isset($error)) {
            ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error;?>
            </div>
            <?php
        }
        ?>
        <?php
        if (isset($message)) {
            ?>
        <p style='color: green;'><?php echo $message;?></p>
            <?php
        }
        ?>
          <?php
            if (isset($messageToPrint)) {
                ?>
        <p style='color: blue;'><?php echo $messageToPrint;?></p>
                <?php
            }
            ?>
        <?php echo file_get_contents('inc/index_forms.html'); ?>
      </div>
  </body>
</html>
