<?php

/**
 * @file
 */

require 'bootstrap.php';

use AWSCognitoApp\AWSCognitoWrapper;

$wrapper = new AWSCognitoWrapper();
$wrapper->initialize();

if (!$wrapper->isAuthenticated()) {
    header('Location: /');
    exit;
}

$user = $wrapper->getUser();
$pool = $wrapper->getPoolMetadata();
$users = $wrapper->getPoolUsers();
?>

<!doctype html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='x-ua-compatible' content='ie=edge'>
        <title>AWS Cognito App - Logged In</title>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <?php echo file_get_contents('inc/head.html'); ?>
    </head>
    <body class="secure-page">
        <?php echo file_get_contents('inc/menu.html'); ?>
        <div class="container mt-5">
          <h2>Secure page</h2>
          <p>Welcome
            <strong><?php echo $user->get('Username');?></strong>!
            You are succesfully authenticated. Some <em>secret</em> information about this user pool:
          </p>

          <h2>Metadata</h2>
          <p><b>Id:</b> <?php echo $pool['Id'];?></p>
          <p><b>Name:</b> <?php echo $pool['Name'];?></p>
          <p><b>CreationDate:</b> <?php echo $pool['CreationDate'];?></p>

          <h2>Users (<?php echo count($users); ?>)</h2>
          <ul>
          <?php
            foreach ($users as $user) {
                $email_attribute_index = array_search('email', array_column($user['Attributes'], 'Name'));
                $email = $user['Attributes'][$email_attribute_index]['Value'];

                echo "<li>{$user['Username']} ({$email})</li>";
            }
            ?>
          </ul>
        </div>
    </body>
</html>
