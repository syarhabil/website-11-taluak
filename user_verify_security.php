<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nik = $_POST['nik'];
    $email = $_POST['email'];
    $security_answer = $_POST['security_answer'];

    $sql = "SELECT security_answer FROM users WHERE nik=? AND email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nik, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['security_answer'] === $security_answer) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Jawaban keamanan tidak cocok.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'NIK dan Email tidak ditemukan.']);
    }

    $stmt->close();
    $conn->close();
}
?>
