<?php

use danog\MadelineProto\API;

$contacts = getContacts();
function getContacts()
{
    $sql = 'SELECT * FROM contacts';
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

if (isset($_GET['sync'])) {
    print_r("Syncing contacts...");
    // Initialize MadelineProto
    $phone = $_SESSION['phone'];
    $sessionName = 'sessions/' . md5($phone);
    $MadelineProto = new API($sessionName);
    $AccountContacts = getFilteredContacts($MadelineProto);
    storeContacts($AccountContacts);
}

function storeContacts($contacts)
{
    $sql = '
        INSERT INTO contacts (user_id, name, family, username, phone) 
        VALUES (:user_id, :name, :family, :username, :phone)
        ON DUPLICATE KEY UPDATE 
        name = VALUES(name), 
        family = VALUES(family), 
        username = VALUES(username), 
        phone = VALUES(phone)
    ';
    $stmt = PDO_CONNECTION->prepare($sql);

    foreach ($contacts as $contact) {
        $stmt->execute([
            'user_id' => $contact['id'],
            'name' => $contact['first_name'] ?? '',
            'family' => $contact['last_name'] ?? '',
            'username' => $contact['username'] ?? '',
            'phone' => $contact['phone'] ?? '',
        ]);
    }
}

if (isset($_GET['delete'])) {
    $userId = $_GET['delete'];
    $sql = 'DELETE FROM contacts WHERE user_id = :user_id';
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    header('Location: syncContacts.php');
    exit();
}
