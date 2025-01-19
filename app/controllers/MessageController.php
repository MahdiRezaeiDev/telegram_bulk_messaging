<?php

use danog\MadelineProto\API;
use danog\MadelineProto\Exception;

// Initialize MadelineProto
$phone = $_SESSION['phone'] ?? null;
if (!$phone) {
    die('Phone number is not set in the session.');
}

$sessionName = 'sessions/' . md5($phone);
$MadelineProto = new API($sessionName);

$lastMessageId = lastMessageId(); // Assuming this function exists and returns the last message ID
$users = getUnsetMessages($lastMessageId);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = htmlspecialchars(trim($_POST['message'])); // Sanitize user input
    storeMessage($message);

    $users = getUnsetMessages();
    if (empty($users)) {
        echo "<script>updateProgress('هیچ مخاطبی پیدا نشد.');</script>";
    } else {
        $lastMessageId = lastMessageId();
        sendMessages($MadelineProto, $users, $message, $lastMessageId);
    }
} elseif (!empty($users)) {
    $message = lastMessage();
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

function markUserAsStrictlyMessaged($userId)
{
    $sql = 'UPDATE contacts SET last_message_id = 0 WHERE user_id = :userId';
    $stmt = PDO_CONNECTION->prepare($sql);
    return $stmt->execute(['userId' => $userId]);
}

function sendMessages($MadelineProto, $users, $message, $lastMessageId)
{
    // Constants for batching and delays
    $BATCH_SIZE = 10;
    $BATCH_DELAY = 180; // Delay between batches in seconds
    $MESSAGE_DELAY = 2; // Delay between individual messages in seconds

    shuffle($users);

    $counter = 0;
    foreach ($users as $user) {
        $userId = $user['user_id'];
        try {
            // Send the message
            $MadelineProto->messages->sendMessage([
                'peer' => $userId,
                'message' => $message,
            ]);

            // Update the last message ID for the user
            updateLastMessageId($userId, $lastMessageId);

            $counter++;

            // Add a delay between messages
            sleep($MESSAGE_DELAY);

            // Handle batch delays
            if ($counter % $BATCH_SIZE === 0) {
                sleep($BATCH_DELAY);
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            markUserAsStrictlyMessaged($userId); // Mark the user to prevent retries
            // Log the error
            logError("Error sending message to user $userId: $errorMessage");
        }
    }
}
