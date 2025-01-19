<?php
// logError function
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

function lastMessageId()
{
    $sql = 'SELECT MAX(id) FROM message';
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getUnsetMessages()
{
    $lastMessageId = lastMessageId(); // Assuming this function exists and returns a value
    $sql = 'SELECT user_id FROM contacts WHERE last_message_id != :lastMessageId AND messaged_allowed = 1';
    $stmt = PDO_CONNECTION->prepare($sql);

    // Execute the query with the binding parameter
    $stmt->execute(['lastMessageId' => $lastMessageId]);

    // Fetch the result as an associative array and return the count
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}

function lastMessage()
{
    $sql = 'SELECT message FROM message ORDER BY id DESC LIMIT 1';
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
}
