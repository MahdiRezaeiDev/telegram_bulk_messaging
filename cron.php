<?php
session_start();

use danog\MadelineProto\API;
use danog\MadelineProto\Exception;

require_once './vendor/autoload.php';
require_once './config/config.php';
require_once './database/db.php';
require_once './app/middlewares/Authorize.php';
require_once './utilities/helper.php';

// Initialize MadelineProto
$phone = '+93728550025';
$sessionName = 'sessions/' . md5($phone);
$MadelineProto = new API($sessionName);
$lastMessageId = lastMessageId(); // Assuming this function exists and returns a value
$users = getUnsetMessages($lastMessageId);


if (count(getUnsetMessages())) {
    $message = lastMessage();
    echo "<script>updateProgress('در حال ارسال پیام های باقی مانده');</script>";
    sendMessages($MadelineProto, $users, $message, $lastMessageId);
}

function storeMessage($message)
{
    $sql = 'INSERT INTO message (message) VALUES (:message)';
    $stmt = PDO_CONNECTION->prepare($sql);
    return $stmt->execute(['message' => $message]);
}

function updateLastMessageId($userId, $lastMessageId)
{
    $sql = 'UPDATE contacts SET last_message_id = :lastMessageId WHERE user_id = :userId';
    $stmt = PDO_CONNECTION->prepare($sql);
    return $stmt->execute(['userId' => $userId, 'lastMessageId' => $lastMessageId]);
}

function sendMessages($MadelineProto, $users, $message, $lastMessageId)
{
    $BATCH_SIZE = 10;
    $counter = 1;
    shuffle($users);
    foreach ($users as $user) {
        $userId = $user['user_id'];

        try {
            // Send the message
            $MadelineProto->messages->sendMessage([
                'peer' => $userId,
                'message' => $message,
            ]);

            // Update the last message ID
            updateLastMessageId($userId, $lastMessageId);

            $counter++;
            if ($counter % $BATCH_SIZE === 0) {
                break;
            }
        } catch (Exception $e) {
            print_r("Hello dude". $e->getMessage());
        }
    }
}
