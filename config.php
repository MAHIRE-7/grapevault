<?php
$host = "shopdb.ch46imawectd.us-east-1.rds.amazonaws.com";
$db_name = "shop_db";
$username = "admin";
$password = "manodayahire";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; 
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


?>


