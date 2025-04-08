<?php include 'header.php';
include 'config.php';

$db = new SQLite3("database/database.sqlite");
$message = '';

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
    }else{
        $_SESSION['id'] = $row['id'];
        $_COOKIE['id'] = $row['id'];
        if ($username == 'admin'){
            $_SESSION['role'] = 'admin';
            $_COOKIE['role'] = 'admin';
        }else{
            $_SESSION['role'] = 'user';
            $_COOKIE['role'] = 'user';
        }
        $message = '<p class="text-green-500">Login successful! Welcome, ' . htmlspecialchars($row['name']) . '</p>';
    }
}

if (isset($_SESSION['id'])){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Adventures</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br overflow-y-scroll h-screen max-w-7xl mx-auto from-blue-50 to-blue-400">
<div class="px-8 py-8 flex lg:w-1/2">
    <div class="flex flex-col text-gray-700 w-full">
        <p class="text-xl font-semibold" >Login / Register </p>
        
        <?php if ($message): ?>
            <div class="mt-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (isVulnerabilityEnabled('sql_injection')): ?>
        <div class="mt-4 bg-yellow-100 p-4 rounded-lg">
            <h3 class="font-semibold mb-2">SQL Injection Demo (Enabled)</h3>
            <p class="text-sm mb-2">Try these SQL injection payloads:</p>
            <ul class="list-disc pl-5 text-sm">
                <li>Username: <code>admin' --</code> (Bypass password)</li>
                <li>Username: <code>' OR '1'='1</code> (Always true)</li>
                <li>Username: <code>' UNION SELECT 1,2,3,4 --</code> (Union injection)</li>
            </ul>
        </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data" class="mt-4">
            <div class="mt-4 flex flex-col space-y-4">
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="username" type="text" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Username"/>
                </div>
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="password" type="password" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Password"/>
                </div>
                <div class="">
                    <button class="bg-blue-500 text-white py-2 px-4 rounded-lg" type="submit">Submit</button>
                </div>
            </div>
        </form>

        <?php if (isVulnerabilityEnabled('sql_injection')): ?>
        <div class="mt-4 bg-red-100 p-4 rounded-lg">
            <h3 class="font-semibold mb-2">Vulnerable Code:</h3>
            <pre class="bg-white p-2 rounded text-sm overflow-x-auto">
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password' LIMIT 1";</pre>
            <p class="text-sm mt-2">This code is vulnerable to SQL injection because it directly concatenates user input into the SQL query.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>


