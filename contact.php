<?php
include 'header.php';
include 'config.php';

$message = '';
$success = false;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $file = $_FILES['file'] ?? null;

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        // Get user directory
        $user_dir = 'uploads/';
        if (isset($_SESSION['id'])) {
            $user_dir .= 'user_' . $_SESSION['id'] . '/';
        } else {
            $user_dir .= 'guest/';
        }

        // Create user directory if it doesn't exist
        if (!file_exists($user_dir)) {
            mkdir($user_dir, 0777, true);
        }

        // Check if file upload vulnerability is enabled
        if (isVulnerabilityEnabled('file_upload')) {
            // VULNERABLE CODE - No file type/size restrictions
            $target_file = $user_dir . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $message = "File uploaded successfully to: " . htmlspecialchars($target_file);
                $success = true;
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        } else {
            // SECURE CODE - With file type/size restrictions
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'text/plain'];
            $max_size = 2 * 1024 * 1024; // 2MB

            if ($file['size'] > $max_size) {
                $message = "File size is too large. Maximum size is 2MB.";
            } elseif (!in_array($file['type'], $allowed_types)) {
                $message = "File type not allowed. Allowed types: jpg, jpeg, png, txt";
            } else {
                $target_file = $user_dir . basename($file['name']);
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    $message = "File uploaded successfully to: " . htmlspecialchars($target_file);
                    $success = true;
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                }
            }
        }
    } else {
        $message = "Please select a file to upload.";
    }
}
?>

<div class="px-8 py-8 flex">
    <div class="flex flex-col text-gray-700 w-full">
        <p class="text-xl font-semibold">Contact Our Team</p>
        <p class="mb-4">Share us your dream destination with more information, pictures and other details for our team to contact you</p>

        <?php if (isVulnerabilityEnabled('file_upload')): ?>
        <div class="bg-yellow-100 p-4 rounded-lg mb-4">
            <h3 class="font-semibold mb-2">File Upload Vulnerability (Enabled)</h3>
            <p class="text-sm mb-2">Try uploading different file types:</p>
            <ul class="list-disc pl-5 text-sm">
                <li>PHP files (.php)</li>
                <li>Shell scripts (.sh)</li>
                <li>Executable files</li>
            </ul>
        </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data" class="mt-4">
            <div class="flex flex-col space-y-4">
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="name" type="text" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Name" required/>
                </div>
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="phone_number" type="tel" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Phone Number" required/>
                </div>
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="email" type="email" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Email" required/>
                </div>
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="file" type="file" class="border-2 border-gray-200 p-2 rounded-lg" required/>
                </div>
                <div class="">
                    <button class="bg-blue-500 text-white py-2 px-4 rounded-lg" type="submit">Submit</button>
                </div>
            </div>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <div class="mt-4 p-4 <?php echo $success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> rounded-lg">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (isVulnerabilityEnabled('file_upload')): ?>
        <div class="mt-4 bg-red-100 p-4 rounded-lg">
            <h3 class="font-semibold mb-2">Vulnerable Code:</h3>
            <pre class="bg-white p-2 rounded text-sm overflow-x-auto">
// No file type/size restrictions
$target_file = $user_dir . basename($file['name']);
move_uploaded_file($file['tmp_name'], $target_file);</pre>
            <p class="text-sm mt-2">This code is vulnerable because it doesn't validate file types or sizes, allowing potentially malicious files to be uploaded.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>
</body>
</html>