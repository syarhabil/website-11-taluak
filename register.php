<?php
session_start();

if (!isset($_SESSION['submit'])) {
    $_SESSION['submit'] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun PPDB SDN 11 Taluak Tahun 2024/2025</title>
    <link href="sb-admin/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        .alert {
            display: none;
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
        .form-control-file {
            padding: 0.375rem 0.75rem;
        }
        .form-margin-bottom {
            margin-bottom: 50px;
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
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header login-header">
                                    <img src="assets/img/logo-tanah-datar.png" alt="Logo Tanah Datar">
                                    <h2>Masuk Akun</h2>
                                    <h3>PPDB SDN 11 Taluak Tahun 2024/2025</h3>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-danger" id="error-alert"></div>
                                    <form id="registerForm" class="form-margin-bottom" enctype="multipart/form-data">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="full_name" name="full_name" type="text" placeholder="Nama Lengkap" required />
                                            <label for="full_name">Nama Lengkap</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="email" name="email" type="email" placeholder="Email" required />
                                            <label for="email">Email</label>
                                        </div>
                                        <div class="form-floating mb-3 password-container">
                                            <input class="form-control password-input" id="password" name="password" type="password" placeholder="Password" required />
                                            <label for="password">Password</label>
                                            <i class="toggle-password fas fa-eye" onclick="togglePassword()"></i>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="nik" name="nik" type="text" placeholder="NIK" maxlength="16" required />
                                            <label for="nik">NIK</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="nisn" name="nisn" type="text" placeholder="NISN" required />
                                            <label for="nisn">NISN</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="phone" name="phone" type="text" placeholder="No. HP" required />
                                            <label for="phone">No. HP</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="grad_year" name="grad_year" type="text" placeholder="Tahun Lulus" required />
                                            <label for="grad_year">Tahun Lulus</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="security_question" name="security_question" type="text" placeholder="Pertanyaan Keamanan" required />
                                            <label for="security_question">Pertanyaan Keamanan</label>
                                            <small class="form-text text-muted">Gunakan pertanyaan yang mudah diingat.</small>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="security_answer" name="security_answer" type="text" placeholder="Jawaban Keamanan" required />
                                            <label for="security_answer">Jawaban Keamanan</label>
                                        </div>
                                        <div class="mb-3">
                                            <label for="photo" class="form-label">Unggah Foto</label>
                                            <input class="form-control form-control-file" id="photo" name="photo" type="file" required />
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="index.php">Sudah punya akun? Masuk</a></div>
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
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();

                var nik = $('#nik').val();
                var nisn = $('#nisn').val();
                var phone = $('#phone').val();
                var grad_year = $('#grad_year').val();

                if (!/^\d+$/.test(nik) || nik.length !== 16) {
                    $('#error-alert').text('NIK harus terdiri dari 16 digit angka.').show();
                    return;
                }

                if (!/^\d+$/.test(nisn)) {
                    $('#error-alert').text('NISN harus berupa angka.').show();
                    return;
                }

                if (!/^\d+$/.test(phone)) {
                    $('#error-alert').text('No. HP harus berupa angka.').show();
                    return;
                }

                if (!/^\d+$/.test(grad_year)) {
                    $('#error-alert').text('Tahun Lulus harus berupa angka.').show();
                    return;
                }

                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: 'submit_register.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Pendaftaran berhasil!',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php';
                            }
                        });
                    },
                    error: function() {
                        $('#error-alert').text('Terjadi kesalahan saat mendaftar, silakan coba lagi.').show();
                    }
                });
            });
        });
    </script>
</body>
</html>
