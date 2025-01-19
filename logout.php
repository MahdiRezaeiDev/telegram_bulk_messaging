<?php
session_start();
$pageTitle = 'خروج';
require_once './vendor/autoload.php';

use danog\MadelineProto\API;

if (isset($_SESSION['phone'])) {
    $phone = $_SESSION['phone'];
    $sessionName = 'sessions/' . md5($phone);

    $MadelineProto = new API($sessionName);

    // Logout from Telegram
    $MadelineProto->logOut();

    // Destroy session
    session_destroy();
}

header('Location: index.php');
exit();
