<?php

$messages = getMessages();

function getMessages()
{
    $sql = 'SELECT message.*, COUNT(contacts.id) AS sendTo 
            FROM message
            LEFT JOIN contacts ON message.id = contacts.last_message_id
            GROUP BY message.id;';
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}
