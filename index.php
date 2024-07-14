<?php
session_start();
include('config.php');

// Inisialisasi pesan error dan sukses
$error_message = '';
$success_message = '';

// Mengecek pesan sukses dari sesi atau parameter URL
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_GET['success_message'])) {
    $success_message = $_GET['success_message'];
}

// Mengecek form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mencari user berdasarkan NIK atau NISN
    $sql = "SELECT * FROM users WHERE (nik=? OR nisn=?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Mengecek hasil query
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Invalid password"; // Pesan error jika password salah
        }
    } else {
        $error_message = "Invalid username"; // Pesan error jika username tidak ditemukan
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Akun PPDB SDN 11 Taluak Tahun 2024/2025</title>
    <link href="sb-admin/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .alert {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .password-container {
            position: relative;
            width: 100%;
        }
        .password-input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px; /* Adjust the maximum width of the modal */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .form-margin-bottom {
            margin-bottom: 10px;
        }
        .footer-margin-top {
            margin-top: 50px;
        }
    </style>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5 form-margin-bottom">
                                <div class="card-header login-header">
                                    <img src="assets/img/logo-tanah-datar.png" alt="Logo Tanah Datar">
                                    <h2>Masuk Akun</h2>
                                    <h3>PPDB SDN 11 Taluak Tahun 2024/2025</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Menampilkan pesan sukses jika ada -->
                                    <?php if (!empty($success_message)): ?>
                                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                                    <?php endif; ?>
                                    <!-- Menampilkan pesan error jika ada dan form sudah disubmit -->
                                    <?php if (!empty($error_message)): ?>
                                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                    <?php endif; ?>
                                    <form method="POST" action="">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="username" name="username" type="text" placeholder="Masukkan NIK/NISN" required />
                                            <label for="username">Masukkan NIK/NISN</label>
                                        </div>
                                        <div class="form-floating mb-3 password-container">
                                            <input class="form-control password-input" id="password" name="password" type="password" placeholder="Password" required />
                                            <label for="password">Password</label>
                                            <i class="toggle-password fas fa-eye" onclick="togglePassword()"></i>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="#" id="forgot-password-link">Lupa Password?</a>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="register.php">Butuh akun? Daftar!</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- The Modal -->
                <div id="forgotPasswordModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Lupa Password</h2>
                        <div class="alert alert-danger" id="forgot-password-error" style="display:none;"></div>
                        <form id="forgotPasswordForm">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="fp-nik" name="fp-nik" type="text" placeholder="NIK" required />
                                <label for="fp-nik">NIK</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="fp-email" name="fp-email" type="email" placeholder="Email" required />
                                <label for="fp-email">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="fp-security-question" name="fp-security-question" type="text" placeholder="Pertanyaan Keamanan" readonly />
                                <label for="fp-security-question">Pertanyaan Keamanan</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="fp-security-answer" name="fp-security-answer" type="text" placeholder="Jawaban Keamanan" required />
                                <label for="fp-security-answer">Jawaban Keamanan</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Verifikasi</button>
                        </form>

                        <form id="resetPasswordForm" style="display:none;">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="new-password" name="new-password" type="password" placeholder="Password Baru" required />
                                <label for="new-password">Password Baru</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="confirm-password" name="confirm-password" type="password" placeholder="Konfirmasi Password" required />
                                <label for="confirm-password">Konfirmasi Password</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Ganti Password</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer" class="footer-margin-top">
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById('password');
            var togglePassword = document.querySelector('.toggle-password');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePassword.classList.remove('fa-eye');
                togglePassword.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                togglePassword.classList.remove('fa-eye-slash');
                togglePassword.classList.add('fa-eye');
            }
        }

        $(document).ready(function() {
            $('#forgot-password-link').on('click', function() {
                $('#forgotPasswordModal').show();
            });

            $('.close').on('click', function() {
                $('#forgotPasswordModal').hide();
            });

            window.onclick = function(event) {
                if (event.target == document.getElementById('forgotPasswordModal')) {
                    $('#forgotPasswordModal').hide();
                }
            };

            $('#fp-nik, #fp-email').on('blur', function() {
                var nik = $('#fp-nik').val();
                var email = $('#fp-email').val();

                if (nik && email) {
                    $.ajax({
                        type: 'POST',
                        url: 'user_verify_nik_email.php',
                        data: { nik: nik, email: email },
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.status === 'success') {
                                var question_length = data.security_question.length;
                                var masked_question = data.security_question.substring(0, 2) + '*'.repeat(question_length - 5) + data.security_question.substring(question_length - 3);
                                $('#fp-security-question').val(masked_question);
                                $('#forgot-password-error').hide();
                            } else {
                                $('#forgot-password-error').text(data.message).show();
                            }
                        },
                        error: function() {
                            $('#forgot-password-error').text('Terjadi kesalahan, silakan coba lagi.').show();
                        }
                    });
                }
            });

            $('#forgotPasswordForm').on('submit', function(e) {
                e.preventDefault();

                var nik = $('#fp-nik').val();
                var email = $('#fp-email').val();
                var security_answer = $('#fp-security-answer').val();

                $.ajax({
                    type: 'POST',
                    url: 'user_verify_security.php',
                    data: { nik: nik, email: email, security_answer: security_answer },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data diverifikasi!',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#forgotPasswordForm').hide();
                                    $('#resetPasswordForm').show();
                                    $('#forgot-password-error').hide();
                                }
                            });
                        } else {
                            $('#forgot-password-error').text(data.message).show();
                        }
                    },
                    error: function() {
                        $('#forgot-password-error').text('Terjadi kesalahan, silakan coba lagi.').show();
                    }
                });
            });

            $('#resetPasswordForm').on('submit', function(e) {
                e.preventDefault();

                var new_password = $('#new-password').val();
                var confirm_password = $('#confirm-password').val();
                var email = $('#fp-email').val();

                if (new_password !== confirm_password) {
                    $('#forgot-password-error').text('Password baru dan konfirmasi password tidak cocok.').show();
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: 'user_reset_password.php',
                    data: { email: email, new_password: new_password },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Password berhasil diganti!',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'index.php';
                                }
                            });
                        } else {
                            $('#forgot-password-error').text(data.message).show();
                        }
                    },
                    error: function() {
                        $('#forgot-password-error').text('Terjadi kesalahan, silakan coba lagi.').show();
                    }
                });
            });
        });
    </script>
</body>
</html>
