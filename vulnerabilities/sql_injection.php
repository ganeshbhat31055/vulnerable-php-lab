<?php
// SQL Injection Vulnerability Demo
// This file demonstrates a basic SQL injection vulnerability
// No other functionality is included to keep the demo focused

// Use the existing database file
$db = new SQLite3("database/database.sqlite");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // VULNERABLE CODE - This is intentionally vulnerable to SQL injection
    // DO NOT USE THIS IN PRODUCTION
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $db->query($query);
    
    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $message = "Login successful! Welcome, " . htmlspecialchars($row['username']);
    } else {
        $message = "Login failed!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SQL Injection Demo</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .container { background: #f5f5f5; padding: 20px; border-radius: 5px; }
        .instructions { background: #fff3cd; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        input { padding: 8px; width: 200px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>SQL Injection Demo</h1>
        
        <div class="instructions">
            <h2>Instructions:</h2>
            <p>This is a demo of SQL injection vulnerability. Try to bypass the login using SQL injection.</p>
            <p>Try these SQL injection payloads:</p>
            <ul>
                <li>Username: admin' --</li>
                <li>Username: ' OR '1'='1</li>
                <li>Username: ' UNION SELECT 1,2,3 --</li>
            </ul>
            <p>Note: This demo uses the project's actual database file (database.sqlite).</p>
        </div>

        <?php if ($message): ?>
            <div style="margin-bottom: 20px; padding: 10px; background: #d4edda; border-radius: 4px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <div style="margin-top: 20px; padding: 15px; background: #f8d7da; border-radius: 4px;">
            <h3>Vulnerable Code:</h3>
            <pre style="background: #fff; padding: 10px; border-radius: 4px;">
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            </pre>
            <p>This code is vulnerable because it directly concatenates user input into the SQL query without any sanitization or parameterization.</p>
        </div>
    </div>
</body>
</html> 