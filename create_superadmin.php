<?php
include('config.php');

// Mengaktifkan pelaporan error
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error_message = '';
$success_message = '';

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nama_lengkap = $_POST['nama_lengkap'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $tanggal_lahir = $_POST['tanggal_lahir'];
        $alamat = $_POST['alamat'];
        $email = $_POST['email'];
        $telepon = $_POST['telepon'];
        $nip = $_POST['nip'];
        $jabatan = 'Kepala Sekolah';
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $foto_profil = $_FILES['foto_profil']['name'];
        $target = "uploads/" . basename($foto_profil);
        $pertanyaan_keamanan = $_POST['pertanyaan_keamanan'];
        $jawaban_keamanan = $_POST['jawaban_keamanan'];

        // Periksa apakah super admin sudah ada
        $check_sql = "SELECT * FROM admins WHERE is_superadmin = 1";
        $result = $conn->query($check_sql);

        if ($result->num_rows > 0) {
            $error_message = "Super admin sudah ada. Hanya satu akun super admin yang diizinkan.";
        } else {
            $sql = "INSERT INTO admins (full_name, gender, dob, address, email, phone, nip, position, username, password, profile_photo, security_question, security_answer, is_verified, is_superadmin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Prepare statement gagal: " . $conn->error);
            }

            $stmt->bind_param("sssssssssssss", $nama_lengkap, $jenis_kelamin, $tanggal_lahir, $alamat, $email, $telepon, $nip, $jabatan, $username, $password, $foto_profil, $pertanyaan_keamanan, $jawaban_keamanan);

            if ($stmt->execute()) {
                if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target)) {
                    $success_message = "Akun super admin berhasil dibuat. Silakan login.";
                    header("Location: superadmin_login.php?success_message=" . urlencode($success_message));
                    exit();
                } else {
                    throw new Exception("Gagal memindahkan file yang diunggah.");
                }
            } else {
                throw new Exception("Execute statement gagal: " . $stmt->error);
            }
        }

        $stmt->close();
        $conn->close();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    error_log($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kepala Sekolah - SDN 11 Taluak</title>
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
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header login-header">
                                    <img src="assets/img/logo-tanah-datar.png" alt="Logo Tanah Datar">
                                    <h2>Buat Akun Kepala Sekolah</h2>
                                    <h3>PPDB SDN 11 Taluak</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($error_message)): ?>
                                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($success_message)): ?>
                                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                                    <?php endif; ?>
                                    <form method="POST" action="create_superadmin.php" enctype="multipart/form-data">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="nama_lengkap" name="nama_lengkap" type="text" placeholder="Nama Lengkap" value="<?php echo isset($_POST['nama_lengkap']) ? $_POST['nama_lengkap'] : ''; ?>" required />
                                            <label for="nama_lengkap">Nama Lengkap</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="Laki-laki" <?php echo (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                                <option value="Perempuan" <?php echo (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                            </select>
                                            <label for="jenis_kelamin">Jenis Kelamin</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="tanggal_lahir" name="tanggal_lahir" type="date" placeholder="Tanggal Lahir" value="<?php echo isset($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : ''; ?>" required />
                                            <label for="tanggal_lahir">Tanggal Lahir</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="alamat" name="alamat" type="text" placeholder="Alamat" value="<?php echo isset($_POST['alamat']) ? $_POST['alamat'] : ''; ?>" required />
                                            <label for="alamat">Alamat</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="email" name="email" type="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required />
                                            <label for="email">Email</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="telepon" name="telepon" type="text" placeholder="Telepon" value="<?php echo isset($_POST['telepon']) ? $_POST['telepon'] : ''; ?>" required />
                                            <label for="telepon">Telepon</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="nip" name="nip" type="text" placeholder="NIP" value="<?php echo isset($_POST['nip']) ? $_POST['nip'] : ''; ?>" />
                                            <label for="nip">NIP</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="jabatan" name="jabatan" type="text" placeholder="Jabatan" value="Kepala Sekolah" readonly />
                                            <label for="jabatan">Jabatan</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="username" name="username" type="text" placeholder="Username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" required />
                                            <label for="username">Username</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="password" name="password" type="password" placeholder="Password" required />
                                            <label for="password">Password</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="confirm_password" name="confirm_password" type="password" placeholder="Konfirmasi Password" required />
                                            <label for="confirm_password">Konfirmasi Password</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="foto_profil" name="foto_profil" type="file" required />
                                            <label for="foto_profil">Unggah Foto Profil</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="pertanyaan_keamanan" name="pertanyaan_keamanan" type="text" placeholder="Pertanyaan Keamanan" value="<?php echo isset($_POST['pertanyaan_keamanan']) ? $_POST['pertanyaan_keamanan'] : ''; ?>" required />
                                            <label for="pertanyaan_keamanan">Pertanyaan Keamanan</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="jawaban_keamanan" name="jawaban_keamanan" type="text" placeholder="Jawaban Keamanan" value="<?php echo isset($_POST['jawaban_keamanan']) ? $_POST['jawaban_keamanan'] : ''; ?>" required />
                                            <label for="jawaban_keamanan">Jawaban Keamanan</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">Buat Akun</button>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="superadmin_login.php">Sudah punya akun? Masuk di sini</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer" style="margin-top: 20px;">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="sb-admin/js/scripts.js"></script>
</body>
</html>