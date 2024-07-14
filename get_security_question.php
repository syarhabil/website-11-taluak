<?php
session_start();
include('config.php');

$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];

    $sql = "SELECT security_question FROM admins WHERE username=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $response['message'] = "Prepare statement failed: " . $conn->error;
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['success'] = true;
        $response['security_question'] = $row['security_question'];
    } else {
        $response['message'] = "Username not found";
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);
?>
