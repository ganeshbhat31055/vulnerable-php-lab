<?php
session_start();
include 'config.php';
include 'header.php';

if (!isRegisterEnabled()) {
    echo '<div class="flex justify-center items-center h-screen"><div class="bg-white p-8 rounded shadow text-center"><p class="text-xl font-semibold text-red-500">Registration is currently disabled.</p></div></div>';
    exit();
}

$db = new SQLite3("database/database.sqlite");
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);

    // Check for duplicate username
    $query = $db->prepare('SELECT id FROM users WHERE username = :username');
    $query->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $query->execute();
    if ($result->fetchArray(SQLITE3_ASSOC)) {
        $message = '<p class="text-red-500">Username already exists.</p>';
    } else {
        $query = $db->prepare('INSERT INTO users (username, password, name) VALUES (:username, :password, :name)');
        $query->bindValue(':username', $username, SQLITE3_TEXT);
        $query->bindValue(':password', $password, SQLITE3_TEXT);
        $query->bindValue(':name', $name, SQLITE3_TEXT);
        $query->execute();
        $message = '<p class="text-green-500">Registration successful! <a href="login.php" class="underline text-blue-600">Login here</a>.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Adventures - Register</title>
    <link href="css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br overflow-y-scroll h-screen max-w-7xl mx-auto from-blue-50 to-blue-400">
<div class="px-8 py-8 flex lg:w-1/2">
    <div class="flex flex-col text-gray-700 w-full">
        <p class="text-xl font-semibold">Register</p>
        <?php if ($message): ?>
            <div class="mt-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="" method="post" class="mt-4">
            <div class="mt-4 flex flex-col space-y-4">
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="name" type="text" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Full Name" required/>
                </div>
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="username" type="text" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Username" required/>
                </div>
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="password" type="password" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Password" required/>
                </div>
                <div class="">
                    <button class="bg-blue-500 text-white py-2 px-4 rounded-lg" type="submit">Register</button>
                </div>
            </div>
        </form>
        <div class="mt-4">
            <a href="login.php" class="text-blue-600 underline">Back to Login</a>
        </div>
    </div>
</div>
<!-- Note: To enable command injection for admin, add COMMAND_INJECTION=true to your .env file -->
</body>
</html> 