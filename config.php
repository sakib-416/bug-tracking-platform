<?php
$host = 'localhost';
$db_user = 'sakib'; 
$db_pass = 'password_here'; // তোর ডাটাবেজের পাসওয়ার্ড এখানে দিবি
$db_name = 'sqli';

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
