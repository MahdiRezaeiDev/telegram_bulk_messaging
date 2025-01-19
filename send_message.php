<?php
session_start();
$pageTitle = 'ارسال پیام';
require_once './vendor/autoload.php';
require_once './config/config.php';
require_once './database/db.php';
require_once './app/middlewares/Authorize.php';
require_once './utilities/helper.php';
require_once './layouts/header.php';
require_once './layouts/nav.php';
if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

?>
<main class="flex items-center justify-center h-screen">
    <div class="bg-white shadow-md rounded p-8 w-full max-w-3xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">ارسال پیام به مخاطبین</h1>
        <form method="post" action="" class="space-y-4" onsubmit="StoreMessage(event)">
            <textarea name="message" rows="8" class="w-full border border-gray-300 rounded-md p-2"
                placeholder="متن پیام خود را وارد کنید..." required></textarea>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md">ارسال پیام</button>
        </form>
        <div id="progress" class="mt-4 text-gray-600"></div>
    </div>
    <script>
        // Ensure updateProgress is defined early
        function updateProgress(message) {
            const progress = document.getElementById('progress');
            if (progress) {
                progress.innerHTML += `<div class="mt-2">${message}</div>`;
            }
        }

        // function StoreMessage(e) {
        //     e.preventDefault();
        //     const message = e.target.message.value;
        //     const params = new URLSearchParams(window.location.search);
        //     params.append('storeMessage', true);
        //     params.append('message', message);

        //     axios.post('./app/api/MessagesApi.php', params).then(response => {
        //         console.log(response.data);
        //         if (response.data.status) {
        //             updateProgress('پیام با موفقیت ارسال شد.');
        //             continueSending();
        //         } else {
        //             updateProgress('خطا در ارسال پیام.');
        //         }
        //     }).catch(error => {
        //         updateProgress('خطا در ارسال پیام.');
        //     });
        // }

        function continueSending() {
            const data = new URLSearchParams();
            data.append('sendMessage', true);

            axios.post('./app/api/MessagesApi.php', data).then(response => {
                console.log(response.data);
            }).catch(error => {
                updateProgress('خطا در ارسال پیام.');
            });
        }
    </script>
</main>
<?php
require_once './app/controllers/MessageController.php';
require_once './layouts/footer.php';
?>