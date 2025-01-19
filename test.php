<?php
session_start();
require_once './vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Exception;

// Configuration
require_once './config/config.php';

// Constants
// define('LOG_FILE', 'error_log.txt'); // Error log file
define('BATCH_SIZE', 15); // Number of messages per batch
define('BATCH_DELAY', 60); // Delay between batches in seconds
define('MESSAGE_DELAY', 1); // Delay between individual messages in seconds

// Set PHP settings
set_time_limit(0); // Allow infinite execution time
ignore_user_abort(true); // Continue execution even if user aborts
ob_implicit_flush(true); // Disable output buffering
ob_end_flush();

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ارسال پیام</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Ensure updateProgress is defined early
        function updateProgress(message) {
            const progress = document.getElementById('progress');
            if (progress) {
                progress.innerHTML += `<div class="mt-2">${message}</div>`;
            }
        }
    </script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded p-8 w-full max-w-xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">ارسال پیام به مخاطبین</h1>
        <form method="post" action="" class="space-y-4">
            <textarea name="message" rows="5" class="w-full border border-gray-300 rounded-md p-2" placeholder="متن پیام خود را وارد کنید..." required></textarea>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md">ارسال پیام</button>
        </form>
        <div id="progress" class="mt-4 text-gray-600"></div>
    </div>
</body>

</html>

<?php

if (!isset($_SESSION['phone']) || !isset($_SESSION['api_id']) || !isset($_SESSION['api_hash'])) {
    header('Location: index.php');
    exit();
}

// Initialize MadelineProto
$phone = $_SESSION['phone'];
$sessionName = 'sessions/' . md5($phone);
$MadelineProto = new API($sessionName);

// Log errors to a file
function logError($message)
{
    file_put_contents(LOG_FILE, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND);
}

// Fetch and filter contacts
function getFilteredContacts($MadelineProto)
{
    try {
        $contacts = $MadelineProto->contacts->getContacts();
        return array_filter($contacts['users'], function ($user) {
            return isset($user['id']) && (!isset($user['bot']) || $user['bot'] === false); // Exclude bots and invalid users
        });
    } catch (Exception $e) {
        logError('Error fetching contacts: ' . $e->getMessage());
        return [];
    }
}

// Send messages in batches
function sendMessages($MadelineProto, $users, $message)
{
    $counter = 0;

    foreach ($users as $user) {
        $userId = $user['id'];

        try {
            // Send the message
            $MadelineProto->messages->sendMessage([
                'peer' => $userId,
                'message' => $message,
            ]);

            $counter++;
            echo "<script>updateProgress('پیام به کاربر با شناسه $userId ارسال شد.');</script>";
            // Small delay between messages
            $start = time();
            while (time() - $start < MESSAGE_DELAY) {
                usleep(100000); // Sleep for 0.1 seconds
            }

            // Delay after each batch
            if ($counter % BATCH_SIZE === 0) {
                echo "<script>updateProgress('ارسال $counter پیام انجام شد، منتظر بمانید...');</script>";
                $start = time();
                while (time() - $start < BATCH_DELAY) {
                    usleep(100000); // Sleep for 0.1 seconds
                }
            }
        } catch (Exception $e) {
            logError("Error sending message to user ID $userId: " . $e->getMessage());
            markUserAsStrictlyMessaged($userId); // Mark the user to prevent retries

            if (strpos($e->getMessage(), 'peer_flood') !== false) {
                echo "<script>updateProgress('به دلیل محدودیت تلگرام، ارسال متوقف شد. لطفا کمی صبر کنید.');</script>";
                break; // Stop on flood limit
            }
        }
    }

    echo "<script>updateProgress('تمام پیام‌ها با موفقیت ارسال شدند.');</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = htmlspecialchars(trim($_POST['message'])); // Sanitize message
    $users = getFilteredContacts($MadelineProto);

    if (empty($users)) {
        echo "<script>updateProgress('هیچ مخاطبی یافت نشد.');</script>";
    } else {
        sendMessages($MadelineProto, $users, $message);
    }
}
function markUserAsStrictlyMessaged($userId)
{
    $sql = 'UPDATE contacts SET last_message_id = 0 WHERE user_id = :userId';
    $stmt = PDO_CONNECTION->prepare($sql);
    return $stmt->execute(['userId' => $userId]);
}
?>