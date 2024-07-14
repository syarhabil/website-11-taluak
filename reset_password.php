<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match";
        header("Location: admin_login.php");
        exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE admins SET password=? WHERE username=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $_SESSION['error_message'] = "Prepare statement failed: " . $conn->error;
        header("Location: admin_login.php");
        exit();
    }

    $stmt->bind_param("ss", $hashed_password, $username);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Password reset successfully";
    } else {
        $_SESSION['error_message'] = "Error resetting password: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: admin_login.php");
    exit();
}
?>
