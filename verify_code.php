<?php
session_start();
$pageTitle = 'وارد کردن کد تایید';
require_once './vendor/autoload.php';
require_once './app/middlewares/Authorize.php';
require_once './app/controllers/VerifyCodeController.php';
require_once './layouts/header.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

?>
<main class="flex items-center justify-center h-screen">
<div class="bg-white shadow-md rounded-lg p-8 w-full max-w-xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">وارد کردن کد تایید</h1>
    <form method="post" action="" class="space-y-4">
        <input type="text" name="code" placeholder="کد تایید را وارد کنید" class="w-full p-2 border border-gray-300 rounded-md" required>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md">ورود</button>
    </form>
</div>
</main>
<?php
require_once './layouts/footer.php';
?>