<?php

$servername = "database-2.cbyhk7lkaodh.us-east-1.rds.amazonaws.com"; 
$username = "aws_user"; 
$password = "Bait9999"; 
$dbname = "speed_db"; 
$bucket = "99speedmartbucket"
$region = "us-east-1"

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
