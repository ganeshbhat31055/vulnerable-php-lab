<?php session_start();?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Hassle Free</title>
    <link href="/assets/app.css" rel="stylesheet">
</head>
<header class="text-gray-700 body-font">
    <div class="container mx-auto flex flex-wrap justify-between py-5 space-x-6 flex-col md:flex-row items-center">
        <a class="flex order-first lg:order-none title-font font-medium items-center text-gray-900 lg:items-center lg:justify-center mb-4 md:mb-0">
            <span class=" text-xl">Island Adventures</span>
        </a>
        <nav class="flex flex-wrap items-center space-x-5 text-base">
            <a class=" hover:text-gray-900" href="/index.php" >Home</a>
            <a class=" hover:text-gray-900" href="/about.php" >About Us</a>
            <a class="hover:text-gray-900" href="/contact.php" >Contact</a>
            <?php
            if (isset($_SESSION['id'])){
                echo '<a class="hover:text-gray-900" href="user/bookings.php" >My Bookings</a>';
                echo '<a href="logout.php" class="inline-flex text-gray-200 items-center bg-red-600 border-0 py-1 px-3 focus:outline-none hover:bg-gray-300 hover:text-base rounded text-base mt-4 md:mt-0">Logout</a>';
            }else{
                echo '<a href="login.php" class="inline-flex text-gray-200 items-center bg-blue-600 border-0 py-1 px-3 focus:outline-none hover:bg-gray-300 hover:text-base rounded text-base mt-4 md:mt-0">Login</a>';
            }
            ?>
        </nav>
    </div>
</header>
