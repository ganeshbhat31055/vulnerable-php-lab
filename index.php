<!DOCTYPE html>
<?php include 'header.php';
$db = new SQLite3("database/database.sqlite");
?>
<html lang="en" >
<body class="bg-gradient-to-br overflow-y-scroll min-h-screen max-w-7xl mx-auto from-blue-50 to-blue-400">

<section class="text-gray-600 body-font">
    <div class="container flex flex-col px-5 mt-8 pb-4 mx-auto">
        <p class="-ml-4 my-4">Travel around the best islands in the world hassle free with us. We offer various services from booking flights,hotels and visa services. All our island tours come with full boarded meals and tour guides. </p>
        <div class="flex flex-col -ml-4 lg:w-1/3">
            <input name="search" type="text" class="border-2 border-gray-200 p-2 rounded-lg" placeholder="Search Places"/>
        </div>

        <div class="grid lg:grid-cols-3 mt-4 gap-4 -m-4">
            <?php $result = $db->query("select * from places");
            while ($row = $result->fetchArray()) {
            ?>
            <div class=" ">
                <div class="h-full bg-gray-50 border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                    <img class="lg:h-48 md:h-36 w-full object-cover object-center" src="<?php echo $row['image_url'] ?>" alt="travel">
                    <div class="p-6">
                        <h2 class="text-base font-medium text-indigo-300 mb-1"><?php echo $row['short_description'] ?></h2>
                        <h1 class="text-2xl font-semibold mb-3"><?php echo $row['name'] ?></h1>
                        <p class="leading-relaxed mb-3"><?php echo $row['description'] ?></p>
                        <div class="flex items-center flex-wrap ">
                            <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">Reviews
                                <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"></path>
                                    <path d="M12 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <span class="text-gray-400 inline-flex items-center leading-none text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <?php echo $row['visitors'] ?> visitors/month
                                </span>
                        </div>
                        <div class="mt-5 inline-flex lg:justify-end">
                            <form method="POST" action="user/bookings.php">
                                <button type="submit" class="inline-flex text-gray-200 items-center bg-blue-600 border-0 py-1 px-3 focus:outline-none hover:bg-gray-300 hover:text-base rounded text-base mt-4 md:mt-0">Book Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>
    <div>
        <div class="container mx-auto px-6">
            <div class="mt-16 border-t-2 border-gray-300 flex flex-col items-center">
                <div class="sm:w-2/3 text-center py-6">
                    <p class="text-sm text-gray-700 font-bold mb-2">© <?php echo date("Y") ?> by Travel Adventures</p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="hidden">ELE{h1ddEn_Cl@sS}</div>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    echo "<div>
        <p class='text-2xl font-semibold text-center'>ELE{Po$7_mEdTHOD}</p>
</div>";
}elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    echo "<div>
        <p class='text-2xl font-semibold text-center'>ELE{dELETe_m3THOD}</p>
</div>";
}
?>
</body>
</html>


