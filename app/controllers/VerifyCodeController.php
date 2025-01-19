<?php

use danog\MadelineProto\API;
use danog\MadelineProto\Exception;
use danog\MadelineProto\Settings\AppInfo;

$phone = $_SESSION['phone'];
$apiId = $_SESSION['api_id'];
$apiHash = $_SESSION['api_hash'];

// Session path
$sessionName = 'sessions' . DIRECTORY_SEPARATOR . md5($phone);

// Initialize MadelineProto
$settings = new AppInfo([
    'api_id' => (int)$apiId,
    'api_hash' => $apiHash,
]);

try {
    $MadelineProto = new API($sessionName, $settings);
} catch (Exception $e) {
    die('Error initializing MadelineProto: ' . $e->getMessage());
}

// Handle the verification code
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['code'])) {
    $code = $_POST['code'];

    try {
        // Complete the login process with the code
        $authorizationState = $MadelineProto->completePhoneLogin($code);

        // Check if a password is required for 2FA
        if ($authorizationState['_'] === 'auth.authorizationSignUpRequired') {
            throw new Exception('This phone number is not registered with Telegram.');
        }

        if ($authorizationState['_'] === 'account.password') {
            // Redirect to password input page
            $_SESSION['auth_password_required'] = true;
            header('Location: verify_password.php');
            exit();
        }

        // If login succeeds, get user info
        $user = $MadelineProto->getSelf();
        if (!$user) {
            throw new Exception('Authentication failed. Please try again.');
        }

        // Save session info
        $_SESSION['user_id'] = $user['id'];

        // Redirect to send message page
        header('Location: send_message.php');
        exit();
    } catch (\danog\MadelineProto\Exception\RPCErrorException $rpcError) {
        // echo 'RPC Error: ' . $rpcError->getMessage();
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
