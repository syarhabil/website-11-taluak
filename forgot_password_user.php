<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $nik = $_POST['nik'];

    // Verifikasi email dan NIK di database
    $sql = "SELECT * FROM users WHERE email='$email' AND nik='$nik'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(50)); // Generate a random token
        $user = $result->fetch_assoc();
        $userId = $user['id'];

        // Store the token in the database with an expiration time
        $sql = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $userId, $token);
        $stmt->execute();

        // Send reset link via email
        $resetLink = "http://localhost/website/reset_password_user.php?token=$token";
        $subject = "Reset Password Anda";
        $message = "Klik link berikut untuk mengatur ulang password Anda: $resetLink";
        $headers = "From: no-reply@sdn11taluak.com";

        if (mail($email, $subject, $message, $headers)) {
            $success = "Link reset password telah dikirim ke email Anda.";
        } else {
            $error = "Gagal mengirim email.";
        }
    } else {
        $error = "Email atau NIK tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SDN 11 Taluak</title>
    <link href="sb-admin/css/styles.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Lupa Password</h3></div>
                                <div class="card-body">
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?php echo $success; ?></div>
                                    <?php endif; ?>
                                    <form method="POST" action="">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="email" name="email" type="email" placeholder="Email" required />
                                            <label for="email">Email</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="nik" name="nik" type="text" placeholder="NIK" required />
                                            <label for="nik">NIK</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">Kirim Link Reset Password</button>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="index.php">Kembali ke Login</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer" class="footer-margin-top">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy; SDN 11 Taluak 2024</div>
                        <div>
                            <a href="#">Kebijakan Privasi</a>
                            &middot;
                            <a href="#">Syarat & Ketentuan</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="sb-admin/js/scripts.js"></script>
</body>
</html>
