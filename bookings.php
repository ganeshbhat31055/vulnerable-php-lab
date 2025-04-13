<?php
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$db = new SQLite3("database/database.sqlite");
$user_id = $_SESSION['id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Build query based on user role
if ($is_admin) {
    // Admin sees all bookings
    $query = "SELECT b.*, u.username, u.name as user_name, p.name as place_name 
              FROM bookings b 
              JOIN users u ON b.user_id = u.id 
              JOIN places p ON b.place_id = p.id 
              ORDER BY b.booking_date DESC";
    $result = $db->query($query);
} else {
    // Regular users see only their bookings
    $query = "SELECT b.*, p.name as place_name 
              FROM bookings b 
              JOIN places p ON b.place_id = p.id 
              WHERE b.user_id = :user_id 
              ORDER BY b.booking_date DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $result = $stmt->execute();
}
?>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold"><?php echo $is_admin ? 'All Bookings' : 'My Bookings'; ?></h1>
</div>

<?php if (isVulnerabilityEnabled('sql_injection')): ?>
<div class="bg-yellow-100 p-4 rounded-lg mb-4">
    <p class="text-sm">
        <?php if ($is_admin): ?>
            SQL Injection is enabled. The flag is hidden in the admin's bookings!
        <?php else: ?>
            SQL Injection is enabled. Try to find a way to see all bookings!
        <?php endif; ?>
    </p>
</div>
<?php endif; ?>

<?php if (isVulnerabilityEnabled('csrf')): ?>
<div class="bg-yellow-100 p-4 rounded-lg mb-4">
    <p class="text-sm">
        CSRF is enabled. Try to create a malicious page that can modify bookings!
    </p>
</div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                <?php if ($is_admin): ?>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <?php endif; ?>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Place</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <?php if ($is_admin): ?>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flag</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['id']); ?></td>
                <?php if ($is_admin): ?>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['user_name']); ?></td>
                <?php endif; ?>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['place_name']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['booking_date']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        <?php echo $row['status'] === 'Confirmed' ? 'bg-green-100 text-green-800' : 
                            ($row['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </span>
                    <?php if ($row['status'] === 'Confirmed'): ?>
                    <form method="POST" class="inline">
                        <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="action" value="cancel">
                        <?php if (!isVulnerabilityEnabled('csrf')): ?>
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <?php endif; ?>
                        <button type="submit" class="ml-2 text-red-600 hover:text-red-900">Cancel</button>
                    </form>
                    <?php endif; ?>
                </td>
                <?php if ($is_admin): ?>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <?php 
                    if ($row['user_id'] === 1) { // Admin's booking
                        echo "ELE{ADMin_bookIn9s_FOUnD}";
                    }
                    ?>
                </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
// Check if booking modification request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['action'])) {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];
    
    if (isVulnerabilityEnabled('csrf')) {
        // VULNERABLE CODE - No CSRF protection
        if ($action === 'cancel') {
            $query = "UPDATE bookings SET status = 'Cancelled' WHERE id = :booking_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':booking_id', $booking_id);
            $stmt->execute();
        }
    } else {
        // SECURE CODE - With CSRF protection
        if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
            if ($action === 'cancel') {
                $query = "UPDATE bookings SET status = 'Cancelled' WHERE id = :booking_id AND user_id = :user_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':booking_id', $booking_id);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
            }
        }
    }
}
?>
</div>
</body>
</html> 