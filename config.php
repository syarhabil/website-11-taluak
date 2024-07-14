<?php
require 'vendor/autoload.php'; // Autoload Composer

use Dotenv\Dotenv;

// Database configuration
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "ppdb";

// Create connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Email configuration
$emailUsername = 'omjokitugas@gmail.com';
?>
