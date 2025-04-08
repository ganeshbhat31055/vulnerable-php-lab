<?php
include 'header.php';
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$db = new SQLite3("database/database.sqlite");

// Get all places from database
$query = "SELECT * FROM places ORDER BY name";
$result = $db->query($query);

// Handle file inclusion
$message = '';
if (isset($_GET['view'])) {
    $file = $_GET['view'];
    
    if (isVulnerabilityEnabled('lfi')) {
        // VULNERABLE CODE - Direct file inclusion without proper validation
        $file_path = 'places/' . $file;
        if (file_exists($file_path)) {
            include $file_path;
        } else {
            $message = '<div class="bg-red-100 p-4 rounded-lg mb-4">Destination details not found.</div>';
        }
    } else {
        // SECURE CODE - Only allow specific files
        $allowed_files = ['bali.txt', 'seychelles.txt', 'bora_bora.txt', 'anguilla.txt', 'aruba.txt'];
        if (in_array($file, $allowed_files)) {
            $file_path = 'places/' . $file;
            if (file_exists($file_path)) {
                include $file_path;
            } else {
                $message = '<div class="bg-red-100 p-4 rounded-lg mb-4">Destination details not found.</div>';
            }
        } else {
            $message = '<div class="bg-red-100 p-4 rounded-lg mb-4">Invalid destination selected.</div>';
        }
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Explore Destinations</h1>

    <?php if (isVulnerabilityEnabled('lfi')): ?>
    <div class="bg-yellow-100 p-4 rounded-lg mb-4">
        <h3 class="font-semibold mb-2">LFI Vulnerability (Enabled)</h3>
        <p class="text-sm mb-2">Try these file paths:</p>
        <ul class="list-disc pl-5 text-sm">
            <li><code>../../../../etc/passwd</code></li>
            <li><code>../../../../etc/hosts</code></li>
            <li><code>../../../../var/log/apache2/access.log</code></li>
            <li><code>../../../../var/log/auth.log</code></li>
        </ul>
    </div>
    <?php endif; ?>

    <?php echo $message; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($place = $result->fetchArray(SQLITE3_ASSOC)): ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if ($place['image']): ?>
            <img src="<?php echo htmlspecialchars($place['image']); ?>" alt="<?php echo htmlspecialchars($place['name']); ?>" class="w-full h-48 object-cover">
            <?php endif; ?>
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($place['name']); ?></h2>
                <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($place['description']); ?></p>
                <p class="text-sm text-gray-500 mb-4">Region: <?php echo htmlspecialchars($place['region']); ?></p>
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold">$<?php echo htmlspecialchars($place['price']); ?> per night</span>
                    <a href="?view=<?php echo urlencode(strtolower(str_replace(' ', '_', $place['name'])) . '.txt'); ?>" 
                       class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        View Details
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <?php if (isVulnerabilityEnabled('lfi')): ?>
    <div class="mt-8 bg-red-100 p-4 rounded-lg">
        <h3 class="font-semibold mb-2">Vulnerable Code:</h3>
        <pre class="bg-white p-2 rounded text-sm overflow-x-auto">
$file_path = 'places/' . $file;
if (file_exists($file_path)) {
    include $file_path;
}</pre>
        <p class="text-sm mt-2">This code is vulnerable to Local File Inclusion because it doesn't properly validate the file path, allowing access to sensitive system files.</p>
    </div>
    <?php endif; ?>
</div>
</div>
</body>
</html> 