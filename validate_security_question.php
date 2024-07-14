<?php
session_start();
include('config.php');

$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $security_answer = $_POST['security_answer'];

    $sql = "SELECT * FROM admins WHERE username=? AND security_answer=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $response['message'] = "Prepare statement failed: " . $conn->error;
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param("ss", $username, $security_answer);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['success'] = true;
        $response['username'] = $row['username'];
        $response['email'] = $row['email'];
    } else {
        $response['message'] = "Invalid security answer";
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);
?>
