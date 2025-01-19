<?php
require_once '../../config/config.php';
require_once '../../database/db.php';
require_once '../../app/middlewares/Authorize.php';
require_once '../../utilities/helper.php';

if (isset($_POST['storeMessage'])) {
    $message = htmlspecialchars(trim($_POST['message'])); // Sanitize message
    header('content-type: application/json');
    echo json_encode(['status' => storeMessage($message)]);
    exit();
}

function storeMessage($message)
{
    $sql = 'INSERT INTO message (message) VALUES (:message)';
    $stmt = PDO_CONNECTION->prepare($sql);
    return $stmt->execute(['message' => $message]);
}
