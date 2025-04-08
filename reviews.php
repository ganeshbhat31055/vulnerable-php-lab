<?php
include 'header.php';
if (isset($_GET['id'])){
    $intval = intval($_GET['id']);

    $db = new SQLite3("database/database.sqlite");

    $query  = $db->prepare('select * from user_reviews where place_id = :place_id');
    $query->bindParam(':place_id',$intval);
    $result = $query->execute();
    $row = $result->fetchArray();

    if (!$row){
        echo 'Error has occurred';
    }else{
        while ($row = $result->fetchArray()) {
            
        }
    }

}
