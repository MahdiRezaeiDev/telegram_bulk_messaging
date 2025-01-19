<?php
session_start();
$pageTitle = 'ورود';
require_once './vendor/autoload.php';
require_once './config/config.php';
require_once './app/middlewares/Authorize.php';
require_once './app/controllers/AuthController.php';
require_once './layouts/header.php';

if (isLoggedIn()) {
    header('Location: send_message.php');
    exit();
}

?>

<div class="bg-white shadow-md rounded-lg p-8 w-full max-w-xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">وارد کردن اطلاعات</h1>
    <form method="post" action="" class="space-y-4">
        <input type="text" name="api_id" placeholder="API ID" class="w-full p-2 border border-gray-300 rounded-md" required>
        <input type="text" name="api_hash" placeholder="API Hash" class="w-full p-2 border border-gray-300 rounded-md" required>
        <input type="text" name="phone" placeholder="شماره تلفن خود را وارد کنید" class="w-full p-2 border border-gray-300 rounded-md" required>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md">ورود</button>
    </form>
</div>
<?php
require_once './layouts/footer.php';
?>