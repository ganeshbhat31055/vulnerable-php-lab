<?php
session_start();
include 'config.php';
include 'header.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$commandInjectionEnabled = function_exists('isCommandInjectionEnabled') && isCommandInjectionEnabled();
$output = '';

if ($commandInjectionEnabled && isset($_POST['host'])) {
    $host = $_POST['host'];
    // Command injection vulnerability: direct concatenation
    $cmd = "ping -c 5 " . $host;
    $output = shell_exec($cmd . " 2>&1");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br overflow-y-scroll h-screen max-w-7xl mx-auto from-blue-50 to-blue-400">
<div class="px-8 py-8 flex lg:w-1/2">
    <div class="flex flex-col text-gray-700 w-full">
        <p class="text-xl font-semibold mb-4">Admin Dashboard</p>
        <?php if ($commandInjectionEnabled): ?>
            <div class="mb-6">
                <form method="post" class="space-y-4">
                    <label class="block font-medium">Ping Host (vulnerable to command injection):</label>
                    <input name="host" type="text" class="border-2 border-gray-200 p-2 rounded-lg w-1/2" placeholder="127.0.0.1" value="<?php echo isset($_POST['host']) ? htmlspecialchars($_POST['host']) : '127.0.0.1'; ?>" required />
                    <button class="bg-blue-500 text-white py-2 px-4 rounded-lg ml-2" type="submit">Ping</button>
                </form>
            </div>
            <?php if ($output): ?>
                <div class="bg-gray-900 text-green-400 font-mono p-4 rounded-lg overflow-x-auto whitespace-pre mt-4">
                    <?php echo htmlspecialchars($output); ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
           
        <?php endif; ?>
    </div>
</div>
</body>
</html>


