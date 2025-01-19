<?php
session_start();
$pageTitle = 'پیام های ثبت شده';
require_once './vendor/autoload.php';
require_once './config/config.php';
require_once './database/db.php';
require_once './app/middlewares/Authorize.php';
require_once './utilities/helper.php';
require_once './app/controllers/archiveController.php';
require_once './layouts/header.php';
require_once './layouts/nav.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}
?>
<main class="pt-20 px-10 h-screen">
    <div class="bg-white shadow-md rounded p-8 max-w-4xl mx-auto">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">پیام‌ها</h1>
        </div>
        <table class="w-full table-auto">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="p-2 text-right w-4">#</th>
                    <th class="p-2 text-right">پیام</th>
                    <th class="p-2 text-right">تعداد دریافت گننده گان</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($messages as $index => $message): ?>
                    <tr class="border-b border-gray-300 even:bg-sky-100">
                        <td class="text-sm font-semibold p-2"><?= $index + 1; ?></td>
                        <td class="text-sm font-semibold p-2"><?= $message['message']; ?></td>
                        <td class="text-sm font-semibold p-2"><?= $message['sendTo']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php
require_once './layouts/footer.php';
?>