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

    $sql = "SELECT * FROM admins WHERE username=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare statement gagal: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['is_verified'] == 1) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin'] = $username;
                $_SESSION['is_superadmin'] = $row['is_superadmin'];
                if ($row['is_superadmin'] == 1) {
                    header("Location: superadmin_dashboard.php");
                } else {
                    header("Location: admin_dashboard.php");
                }
                exit();
            } else {
                $error_message = "Invalid password";
            }
        } elseif ($row['is_verified'] == 0) {
            $error_message = "Akun Anda sedang menunggu persetujuan kepala sekolah";
        } else {
            $error_message = "Akun Anda telah ditolak";
        }
    } else {
        $error_message = "Invalid username";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SDN 11 Taluak</title>
    <link href="sb-admin/css/styles.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
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
                                    <h2>Admin Login</h2>
                                    <h3>PPDB SDN 11 TALUAK</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($success_message)): ?>
                                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($error_message)): ?>
                                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                    <?php endif; ?>
                                    <form method="POST" action="admin_login.php">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="username" name="username" type="text" placeholder="Username" required />
                                            <label for="username">Username</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="password" name="password" type="password" placeholder="Password" required />
                                            <label for="password">Password</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="admin_register.php">Need an account? Sign up!</a></div>
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

    <!-- Modal for forgot password -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="securityQuestionForm" method="POST" action="validate_security_question.php">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="securityUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="securityQuestion" class="form-label">Security Question</label>
                            <input type="text" class="form-control" id="securityQuestion" name="security_question" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="securityAnswer" class="form-label">Security Answer</label>
                            <input type="text" class="form-control" id="securityAnswer" name="security_answer" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                    <form id="resetPasswordForm" method="POST" action="reset_password.php" style="display:none;">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="resetUsername" name="username" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="resetEmail" name="email" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="sb-admin/js/scripts.js"></script>

    <?php if (!empty($error_message)): ?>
    <script>
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {});
        errorModal.show();
    </script>
    <?php endif; ?>

    <script>
        document.getElementById('securityUsername').addEventListener('blur', function() {
            var username = this.value;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_security_question.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        var question = response.security_question;
                        var hiddenQuestion = question.split('').map((char, index) => {
                            return (index % 2 === 0) ? char : '*';
                        }).join('');
                        document.getElementById('securityQuestion').value = hiddenQuestion;
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send('username=' + username);
        });

        document.getElementById('securityQuestionForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var username = document.getElementById('securityUsername').value;
            var security_answer = document.getElementById('securityAnswer').value;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'validate_security_question.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.getElementById('securityQuestionForm').style.display = 'none';
                        document.getElementById('resetPasswordForm').style.display = 'block';
                        document.getElementById('resetUsername').value = response.username;
                        document.getElementById('resetEmail').value = response.email;
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send('username=' + username + '&security_answer=' + security_answer);
        });
    </script>
</body>
</html>
