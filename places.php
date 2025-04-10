<!DOCTYPE html>
<?php include 'header.php'; ?>
<html lang="en">
<body class="bg-gradient-to-br overflow-y-scroll min-h-screen max-w-7xl mx-auto from-blue-50 to-blue-400">

<section class="text-gray-600 body-font">
    <div class="container px-5 py-8 mx-auto">
        <?php
        // Check if search parameter exists
        if (isset($_GET['search'])) {
            $search = $_GET['search'];
            
            // Display educational message about SQL injection
            if (isVulnerabilityEnabled('sql_injection')) {
                echo '<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                    <p class="font-bold">SQL Injection Vulnerability Enabled</p>
                    <p>Try to use sqlmap to extract data from the database!</p>
                    <p class="text-sm mt-2">Hint: There might be a flags table somewhere...</p>
                    <p class="text-sm">Example sqlmap command:</p>
                    <code class="block bg-yellow-50 p-2 mt-1">sqlmap -u "http://your-site/places.php?search=test" --batch --dbs</code>
                </div>';
                
                // Vulnerable query - direct concatenation
                $query = "SELECT name, description FROM places WHERE name LIKE '%" . $search . "%'";
            } else {
                // Secure query - using prepared statements
                $query = "SELECT name, description FROM places WHERE name LIKE :search";
            }
            
            try {
                $db = new SQLite3("database/database.sqlite");
                
                if (isVulnerabilityEnabled('sql_injection')) {
                    $result = $db->query($query);
                } else {
                    $stmt = $db->prepare($query);
                    $stmt->bindValue(':search', '%' . $search . '%', SQLITE3_TEXT);
                    $result = $stmt->execute();
                }
                
                echo '<div class="bg-white rounded-lg shadow-lg p-6">';
                echo '<h2 class="text-2xl font-bold mb-4">Search Results</h2>';
                
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    echo '<div class="mb-4 p-4 border rounded">';
                    echo '<h3 class="text-xl font-semibold">' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p class="mt-2">' . htmlspecialchars($row['description']) . '</p>';
                    echo '</div>';
                }
                
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">Error</p>
                    <p>An error occurred while searching.</p>
                </div>';
            }
        }
        ?>
        
        <!-- Search Form -->
        <div class="mb-8">
            <form class="flex gap-4 items-center justify-center">
                <input type="text" name="search" placeholder="Search places..." 
                       class="px-4 py-2 border rounded-lg w-full max-w-md"
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" 
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                    Search
                </button>
            </form>
        </div>
        
        <?php
        // Original LFI vulnerability code
        if (isset($_GET['view'])) {
            $file = $_GET['view'];
            $base_dir = 'places/';
            $full_path = $base_dir . $file;
            
            // Display educational message about LFI
            if (isVulnerabilityEnabled('lfi')) {
                echo '<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Local File Inclusion (LFI) Demo Enabled</p>
                    <p>Try to find the hidden flag by exploring different file paths!</p>
                    <p class="text-sm mt-2">Hint: The flag is in a configuration file...</p>
                </div>';
            }

            if (file_exists($full_path) && strpos(realpath($full_path), realpath($base_dir)) === 0) {
                echo '<div class="bg-white rounded-lg shadow-lg p-6">';
                echo '<h1 class="text-3xl font-bold mb-4">' . ucwords(str_replace('_', ' ', basename($file, '.txt'))) . '</h1>';
                echo '<div class="prose max-w-none">';
                echo nl2br(file_get_contents($full_path));
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">Error</p>
                    <p>Place details not found. Please try again.</p>
                </div>';
            }
        } else {
            // If no view parameter is provided
            echo '<div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                <p class="font-bold">No Place Selected</p>
                <p>Please select a place to view its details.</p>
            </div>';
        }
        ?>
    </div>
</section>

<div class="hidden">ELE{LFI_Fl@g_1s_H3r3}</div>

</body>
</html> 