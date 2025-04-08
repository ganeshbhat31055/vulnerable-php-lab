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
        $message = "Login successful! Welcome, " . htmlspecialchars($row['name']);
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
        .user-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .user-table th, .user-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .user-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>SQL Injection Demo</h1>
        
        <div class="instructions">
            <h2>Instructions:</h2>
            <p>This is a demo of SQL injection vulnerability. Try to bypass the login using SQL injection.</p>
            
            <h3>Valid Test Users:</h3>
            <table class="user-table">
                <tr>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Name</th>
                </tr>
                <tr>
                    <td>admin</td>
                    <td>admin</td>
                    <td>Administrator</td>
                </tr>
                <tr>
                    <td>user1</td>
                    <td>password1</td>
                    <td>Test User 1</td>
                </tr>
                <tr>
                    <td>user2</td>
                    <td>password2</td>
                    <td>Test User 2</td>
                </tr>
                <tr>
                    <td>guest</td>
                    <td>guest123</td>
                    <td>Guest User</td>
                </tr>
            </table>

            <h3>SQL Injection Payloads to Try:</h3>
            <ul>
                <li>Username: <code>admin' --</code> (Bypass password check)</li>
                <li>Username: <code>' OR '1'='1</code> (Always true condition)</li>
                <li>Username: <code>' UNION SELECT 1,2,3,4 --</code> (Union-based injection)</li>
                <li>Username: <code>' OR 1=1; --</code> (Alternative true condition)</li>
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