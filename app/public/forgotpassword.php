<?php

/**
 * @file
 */

require 'bootstrap.php';

use AWSCognitoApp\AWSCognitoWrapper;

session_start();

$wrapper = new AWSCognitoWrapper();
$wrapper->initialize();

$entercode = false;

if (isset($_POST['action'])) {
    if ($_POST['action'] === 'code') {
        $username = $_POST['username'] ?? '';

        $error = $wrapper->sendPasswordResetMail($username);

        if (empty($error)) {
            header('Location: forgotpassword.php?username=' . $username);
        }
    }

    if ($_POST['action'] == 'reset') {
        $code = $_POST['code'] ?? '';
        $password = $_POST['password'] ?? '';
        $username = $_GET['username'] ?? '';

        $error = $wrapper->resetPassword($code, $password, $username);

      // @todo show message on new page that password has been reset.
        if (empty($error)) {
            header('Location: index.php?reset');
        }
    }
}

if (isset($_GET['username'])) {
    $entercode = true;
}
?>

<!doctype html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='x-ua-compatible' content='ie=edge'>
        <title>AWS Cognito App - Forgot Password</title>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <?php echo file_get_contents('inc/head.html'); ?>
    </head>
    <body class="<?php echo (isset($_SESSION['status'])) ? $_SESSION['status'] : '' ?>">
        <?php echo file_get_contents('inc/menu.html'); ?>
        <div class="container mt-5">
            <?php
            if (isset($error)) {
                ?>
            <p style='color: red;'><?php echo $error;?></p>
                <?php
            }
            ?>
            <?php if ($entercode) {
                echo file_get_contents('inc/forgotten_resetform.html');
            } else {
                echo file_get_contents('inc/forgotten_forgotform.html');
            }?>
        </div>
</body>
</html>
