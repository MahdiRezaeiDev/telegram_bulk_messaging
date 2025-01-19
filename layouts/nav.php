<nav id="main_nav" class="fixed top-0 left-0 right-0 z-50 p-2 flex justify-between bg-white shadow-md">
    <ul class="flex items-center">
        <li class="mx-1 hidden sm:block text-sm font-bold bg-gray-100 hover:bg-gray-200">
            <a class="p-2 flex items-center gap-3" href="./send_message.php">
                ارسال پیام
                <img class="hidden sm:inline-block" src="./public/img/message.svg" alt="telegram icon">
            </a>
        </li>
        <li class="mx-1 hidden sm:block text-sm font-bold bg-gray-100 hover:bg-gray-200">
            <a class="p-2 flex items-center gap-3" href="./syncContacts.php">
                بروزرسانی مخاطبین
                <img class="hidden sm:inline-block" src="./public/img/contacts.svg" alt="telegram icon">
            </a>
        </li>
        <li class="mx-1 hidden sm:block text-sm font-bold bg-gray-100 hover:bg-gray-200">
            <a class="p-2 flex items-center gap-3" href="./archive.php">
                پیام های ثبت شده
                <img class="hidden sm:inline-block" src="./public/img/archive.svg" alt="telegram icon">
            </a>
        </li>
    </ul>
    <div class="hidden sm:flex items-center">
        <a class="mx-1 hidden sm:block text-sm font-bold bg-gray-100 hover:bg-gray-200 p-2 flex items-center gap-3" href="./logout.php">
            خروج
            <img class="hidden sm:inline-block" src="./public/img/logout.svg" alt="telegram icon">
        </a>
    </div>
</nav>