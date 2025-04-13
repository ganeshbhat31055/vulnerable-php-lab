<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Adventure</title>
    <link href="css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br overflow-y-scroll min-h-screen max-w-7xl mx-auto from-blue-50 to-blue-400">
    <header class="text-gray-600 body-font">
        <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
            <a href="index.php" class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-10 h-10 text-white p-2 bg-indigo-500 rounded-full" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                </svg>
                <span class="ml-3 text-xl">Travel Adventure</span>
            </a>
            <nav class="md:ml-auto flex flex-wrap items-center text-base justify-center">
                <a class="mr-5 hover:text-gray-900" href="index.php">Home</a>
                <a class="mr-5 hover:text-gray-900" href="places.php">Destinations</a>
                <a class="mr-5 hover:text-gray-900" href="contact.php">Contact</a>
                <?php if (isset($_SESSION['id'])): ?>
                        <a class="mr-5 hover:text-gray-900" href="/dashboard.php">Dashboard</a>
                        <a class="mr-5 hover:text-gray-900" href="/bookings.php">Bookings</a>
                
                    <a class="inline-flex text-gray-200 items-center bg-red-600 border-0 py-1 px-3 focus:outline-none hover:bg-gray-300 hover:text-base rounded text-base mt-4 md:mt-0" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="inline-flex text-gray-200 items-center bg-blue-600 border-0 py-1 px-3 focus:outline-none hover:bg-gray-300 hover:text-base rounded text-base mt-4 md:mt-0" href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <div class="container mx-auto px-4 py-8">
