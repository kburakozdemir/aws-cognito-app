<?php

/**
 * @file
 */

require 'bootstrap.php';

use AWSCognitoApp\AWSCognitoWrapper;

session_start();

$wrapper = new AWSCognitoWrapper();
$wrapper->initialize();

if (isset($_POST['action'])) {
    $username = $_POST['username'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    $error = $wrapper->confirmSignup($username, $confirmation);

    if (empty($error)) {
        header('Location: secure.php');
    }
}

$username = $_GET['username'] ?? '';
?>

<!doctype html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='x-ua-compatible' content='ie=edge'>
        <title>AWS Cognito App - Register and Login, Confirm Signup</title>
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
            <h2>Confirm signup</h2>
            <form method='post' action=''>
                <input type='text' placeholder='Username' name='username' value='<?php echo $username;?>' required /><br />
                <input type='text' placeholder='Confirmation code' name='confirmation' required /><br />
                <input type='hidden' name='action' value='confirm' />
                <input type='submit' value='Confirm' />
            </form>
        </div>
    </body>
</html>
