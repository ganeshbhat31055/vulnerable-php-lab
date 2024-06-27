<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $success = false;

    $name = is_null($_POST['name']) ? '' : $_POST['name'];
    $phone = is_null($_POST['phone_number']) ? 0 : $_POST['phone_number'];
    $email = is_null($_POST['email']) ? '' : $_POST['email'];

    //store the file in the server uploads directory and get the file path

    $file = $_FILES['file'];
    //check if the file size is less than 2 MB and the file type is image,txt or php

    $fileOk = true;

    $message = '';

    //check if file is uploaded
    if($file['size'] == 0){
        $message = "Please upload a valid file";
        $fileOk = false;
    }else{
        if($file['size'] > 2*1024*1024){
            $message =   "File size is too large. Please upload a file less than 2MB";
            $fileOk = false;
        }
        if (!in_array($file['type'], ['image/jpeg', 'image/png', 'image/jpg','text/plain','application/php'])){
            $message =  "File type not supported. Please upload a file of type jpg, jpeg or png";
            $fileOk = false;
        }
    }





    if ($fileOk && move_uploaded_file($file['tmp_name'], './uploads/'.$file['name'])){
        $file_path = './uploads/'.$file['name'];
        $message = "Data Submitted Successfully and file uploaded to server ".$file_path;
    }

    $success = true;
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
<?php include 'header.php';?>
<div class="px-8 py-8 flex">
    <div class="flex flex-col text-gray-700">
        <p class="text-xl font-semibold" >Contact Our Team : </p>
        <p class="" >Share us your dream destination with our more information, Share pictures and other details for our team to contact you</p>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mt-4 flex flex-col space-y-4">
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="name" type="text" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Name"/>
                </div>
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="phone_number" type="number" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Phone Number"/>
                </div>
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="email" type="email" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Email"/>
                </div>
                <div class="flex flex-col w-1/2 space-y-2">
                    <input name="file" type="file" class="border-2 border-gray-100 p-2 rounded-lg" placeholder="Destination"/>
                </div>
                <div class="">
                    <button class="bg-blue-500 text-white py-2 px-4 rounded-lg" type="submit">Submit</button>
                </div>
            </div>
        </form>
        <?php
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                if(!$success){
                    echo '<p class="mt-5 text-red-500">Form Submission Failed</p>';
                }else{
                    echo '<p class="mt-5 text-green-800">'.$message.'</p>';
                }
            }
        ?>
    </div>
</div>
</body>
</html>