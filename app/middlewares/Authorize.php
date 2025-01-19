<?php
function isLoggedIn()
{
    return isset($_SESSION['api_hash']) && isset($_SESSION['api_id']) && isset($_SESSION['phone']);
}
