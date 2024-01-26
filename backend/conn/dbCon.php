<?php
// db_connection.php

header("Access-Control-Allow-Origin: *");

$servername = "/* host_name */";
$username = "/* username */";
$password = "/* password */";
$dbname = "/* database_name */";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}