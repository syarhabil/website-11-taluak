<?php
session_start();
include('config.php');

$error_message = '';
$success_message = '';

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_GET['success_message'])) {
    $success_message = $_GET['success_message'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE username=? AND is_superadmin=1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare statement gagal: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $username;
            $_SESSION['is_superadmin'] = 1;
            header("Location: superadmin_dashboard.php");
            exit();
        } else {
            $error_message = "Password salah";
        }
    } else {
        $error_message = "Username salah";
    }

    $stmt->close();
    $conn->close();
}

// Periksa apakah super admin sudah ada
$superadmin_exists = false;
$check_sql = "SELECT * FROM admins WHERE is_superadmin = 1";
$result = $conn->query($check_sql);
if ($result->num_rows > 0) {
    $superadmin_exists = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Super Admin - SDN 11 Taluak</title>
    <link href="sb-admin/css/styles.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script>
        function checkSuperAdminExists() {
            if (<?php echo $superadmin_exists ? 'true' : 'false'; ?>) {
                alert("Akun kepala sekolah telah ada, akun hanya bisa dibuat satu kali, hapus akun yang ada terlebih dahulu di dashboard.");
                return false;
            }
            return true;
        }
    </script>
     <style>
        .login-header {
            text-align: center;
        }
        .login-header img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .login-header h2 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }
        .login-header h3 {
            margin: 0;
            font-size: 1.25rem;
            color: #555;
        }
    </style>
</head>
<body style="background-color: #ecb02e !important;">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                            <div class="card-header login-header">
                                    <img src="assets/img/logo-tanah-datar.png" alt="Logo Tanah Datar">
                                    <h2>Masuk Akun Kepala Sekolah</h2>
                                    <h3>PPDB SDN 11 Taluak</h3>
                                </div>                                <div class="card-body">
                                    <?php if (!empty($success_message)): ?>
                                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($error_message)): ?>
                                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                    <?php endif; ?>
                                    <form method="POST" action="superadmin_login.php">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="username" name="username" type="text" placeholder="Username" required />
                                            <label for="username">Username</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="password" name="password" type="password" placeholder="Password" required />
                                            <label for="password">Password</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="create_superadmin.php" onclick="return checkSuperAdminExists();">Belum punya akun? Daftar di sini</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
        <footer class="py-4 bg-light mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex flex-column align-items-center justify-content-center small text-center">
            <div class="text-muted">Copyright &copy; SD 11 Taluak 2024</div>
            <div>
                Made with <i class="fas fa-heart" style="color: red;"></i> by Kelompok 3 PNP Tanah Datar Angkatan 2022
            </div>
            <div>
                <span>v.1.0</span>
            </div>
        </div>
    </div>
</footer>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="sb-admin/js/scripts.js"></script>
</body>
</html>
