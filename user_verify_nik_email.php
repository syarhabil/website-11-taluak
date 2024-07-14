<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nik = $_POST['nik'];
    $email = $_POST['email'];

    $sql = "SELECT email, security_question FROM users WHERE nik=? AND email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nik, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $question_length = strlen($row['security_question']);
        $masked_question = substr($row['security_question'], 0, 2) . str_repeat('*', $question_length - 5) . substr($row['security_question'], -3);
        echo json_encode(['status' => 'success', 'security_question' => $masked_question]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'NIK dan Email tidak ditemukan.']);
    }

    $stmt->close();
    $conn->close();
}
?>
