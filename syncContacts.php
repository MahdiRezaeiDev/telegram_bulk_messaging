<?php
session_start();
$pageTitle = 'بروزرسانی مخاطبین';
require_once './vendor/autoload.php';
require_once './config/config.php';
require_once './database/db.php';
require_once './app/middlewares/Authorize.php';
require_once './utilities/helper.php';
require_once './app/controllers/contactsController.php';
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
            <h1 class="text-2xl font-bold text-gray-800 mb-4">مخاطبین</h1>
            <a href="?sync=true" class="bg-sky-600 text-white rounded shadow hover:bg-sky-700 hover:shadow-lg px-5 py-1">بروزرسانی </a>
        </div>
        <table class="w-full table-auto">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="p-2 text-right">#</th>
                    <th class="p-2 text-right">نام</th>
                    <th class="p-2 text-right">نام خانوادگی</th>
                    <th class="p-2 text-right">شماره تلفن</th>
                    <th class="p-2 text-right">نام کاربری</th>
                    <th class="p-2 text-right">شناسه یکتا</th>
                    <th class="p-2 text-right">عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($contacts as $index => $contact): ?>
                    <tr class="border-b border-gray-300 even:bg-sky-100">
                        <td class="text-sm font-semibold p-2"><?= $index + 1; ?></td>
                        <td class="text-sm font-semibold p-2"><?= $contact['name']; ?></td>
                        <td class="text-sm font-semibold p-2"><?= $contact['family']; ?></td>
                        <td class="text-sm font-semibold p-2"><?= $contact['phone']; ?></td>
                        <td class="text-sm font-semibold p-2"><?= $contact['userName']; ?></td>
                        <td class="text-sm font-semibold p-2"><?= $contact['user_id']; ?></td>
                        <td class="text-sm font-semibold p-2">
                            <div class="flex justify-between items-center">
                                <a href="send_message.php?user_id=<?= $contact['user_id']; ?>" title="ارسال پیام">
                                    <img class="w-5 h-5" src="./public/img/favIcon.svg" alt="send message icon">
                                </a>
                                <a href="syncContacts.php?delete=<?= $contact['user_id']; ?>" title="حذف مخاطب">
                                    <img class="w-5 h-5" src="./public/img/delete.svg" alt="delete icon">
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php
require_once './layouts/footer.php';
?>