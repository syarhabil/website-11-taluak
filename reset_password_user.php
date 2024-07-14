<?php
session_start();
include('config.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $sql = "SELECT * FROM password_resets WHERE token='$token' AND expires_at > NOW()";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['user_id'];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            $userSql = "SELECT password FROM users WHERE id='$userId'";
            $userResult = $conn->query($userSql);
            $userRow = $userResult->fetch_assoc();

            if (password_verify($password, $userRow['password'])) {
                $error = "Password baru tidak boleh sama dengan password sebelumnya.";
            } elseif ($password === $confirm_password) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password='$hashedPassword' WHERE id='$userId'";

                if ($conn->query($sql) === TRUE) {
                    $sql = "DELETE FROM password_resets WHERE user_id='$userId'";
                    $conn->query($sql);
                    $success = "Password berhasil direset. Silakan login.";
                } else {
                    $error = "Terjadi kesalahan saat mereset password.";
                }
            } else {
                $error = "Password dan Konfirmasi Password tidak sesuai.";
            }
        }
    } else {
        $error = "Token tidak valid atau telah kadaluarsa.";
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SDN 11 Taluak</title>
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
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Reset Password</h3></div>
                                <div class="card-body">
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?php echo $success; ?></div>
                                    <?php endif; ?>
                                    <form method="POST" action="">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="password" name="password" type="password" placeholder="Password Baru" required />
                                            <label for="password">Password Baru</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="confirm_password" name="confirm_password" type="password" placeholder="Konfirmasi Password Baru" required />
                                            <label for="confirm_password">Konfirmasi Password Baru</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
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
