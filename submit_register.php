<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('config.php');

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nik = $_POST['nik'];
        $nisn = $_POST['nisn'];
        $phone = $_POST['phone'];
        $grad_year = $_POST['grad_year'];
        $security_question = $_POST['security_question'];
        $security_answer = $_POST['security_answer'];
        $photo = $_FILES['photo']['name'];
        $target = "uploads/" . basename($photo);

        if (!isset($_FILES['photo']['tmp_name']) || $_FILES['photo']['tmp_name'] == '') {
            throw new Exception("File upload error: No file uploaded.");
        }

        $sql = "INSERT INTO users (full_name, email, password, nik, nisn, phone, grad_year, security_question, security_answer, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("ssssssssss", $full_name, $email, $password, $nik, $nisn, $phone, $grad_year, $security_question, $security_answer, $photo);

        if ($stmt->execute()) {
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                throw new Exception("Failed to move uploaded file.");
            }

            echo json_encode(['status' => 'success']);
        } else {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        error_log($e->getMessage());
    }
}
?>
