<?php 
session_start();
include 'config.php';
include 'header.php';

$db = new SQLite3("database/database.sqlite");
$message = '';

// Check if already logged in
if (isset($_SESSION['id'])) {
    header("Location: bookings.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isVulnerabilityEnabled('sql_injection')) {
        // VULNERABLE CODE - This is intentionally vulnerable to SQL injection
        // DO NOT USE THIS IN PRODUCTION
        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password' LIMIT 1";
        $result = $db->query($query);
    } else {
        // SECURE CODE - Using prepared statements
        $query = $db->prepare('SELECT * FROM users WHERE username = :username AND password = :password LIMIT 1');
        $query->bindParam(':username', $username);
        $query->bindParam(':password', $password);
        $result = $query->execute();
    }
    
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    if(!$row){
        $message = '<p class="text-red-500">Login failed</p>';
    } else {

        echo $row['username'];
        // Set session variables
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['name'] = $row['name'];
        
        // Set role based on username
        if ($_SESSION['username'] === 'admin') {
            $_SESSION['role'] = 'admin';
        } else {
            $_SESSION['role'] = 'user';
        }
        
        // Redirect to bookings page
        header("Location: bookings.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Adventures - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br overflow-y-scroll h-screen max-w-7xl mx-auto from-blue-50 to-blue-400">
<div class="px-8 py-8 flex lg:w-1/2">
    <div class="flex flex-col text-gray-700 w-full">
        <p class="text-xl font-semibold">Login</p>
        
        <?php if ($message): ?>
            <div class="mt-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="mt-4">
            <div class="mt-4 flex flex-col space-y-4">
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="username" type="text" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Username" required/>
                </div>
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="password" type="password" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Password" required/>
                </div>
                <div class="">
                    <button class="bg-blue-500 text-white py-2 px-4 rounded-lg" type="submit">Login</button>
                </div>
            </div>
        </form>

    </div>
</div>
</body>
</html>


