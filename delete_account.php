<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include('config.php');

$username = $_SESSION['username'];

// Query untuk menghapus akun berdasarkan NIK atau NISN
$sql = "DELETE FROM users WHERE nik='$username' OR nisn='$username'";
if ($conn->query($sql) === TRUE) {
    session_destroy();
    header("Location: index.php");
    exit();
} else {
    echo "Error deleting record: " . $conn->error;
}
?>
