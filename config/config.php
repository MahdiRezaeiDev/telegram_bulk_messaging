<?php
define('API_ID', 'your_api_id');  // Your Telegram API ID
define('API_HASH', 'your_api_hash'); // Your Telegram API Hash
define('LOG_FILE', 'error_log.txt'); // Error log file

$sessionDir = 'sessions';
if (!is_dir($sessionDir)) {
    mkdir($sessionDir, 0777, true);
}

$host = 'localhost';
$dbname = 'messages';
$username = 'root';
$password = '';
