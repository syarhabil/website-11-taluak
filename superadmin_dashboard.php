<?php
session_start();
include('config.php');

// Periksa apakah pengguna adalah super admin
if (!isset($_SESSION['admin']) || !$_SESSION['is_superadmin']) {
    header("Location: superadmin_login.php");
    exit();
}

// Ambil data admin yang belum diverifikasi
$sql = "SELECT * FROM admins WHERE is_verified = 0";
$result = $conn->query($sql);

// Ambil data pengguna dan data Excel yang diunggah
$sql_users = "SELECT * FROM users";
$result_users = $conn->query($sql_users);

$sql_excel = "SELECT * FROM excel_data";
$result_excel = $conn->query($sql_excel);

// Ambil data admin yang sudah diverifikasi dan ditolak untuk riwayat
$sql_approved_admins = "SELECT * FROM admins WHERE is_verified = 1";
$result_approved_admins = $conn->query($sql_approved_admins);

$sql_rejected_admins = "SELECT * FROM admins WHERE is_verified = -1";
$result_rejected_admins = $conn->query($sql_rejected_admins);

// Ambil data super admin yang terverifikasi
$sql_superadmin = "SELECT * FROM admins WHERE is_verified = 1 AND is_superadmin = 1";
$result_superadmin = $conn->query($sql_superadmin);
$superadmin = $result_superadmin->fetch_assoc();

// Jika ada tindakan approve atau reject
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $admin_id = $_POST['admin_id'];
        $sql = "UPDATE admins SET is_verified = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['reject'])) {
        $admin_id = $_POST['admin_id'];
        $sql = "UPDATE admins SET is_verified = -1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_superadmin'])) {
        $security_answer = $_POST['security_answer'];
        $username = $_SESSION['admin'];

        // Periksa jawaban pertanyaan keamanan
        $sql = "SELECT security_answer FROM admins WHERE username = ? AND is_superadmin = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row && $row['security_answer'] == $security_answer) {
            echo "<script>
                if (confirm('Konfirmasi apakah melanjutkan? Ketika hapus akun, maka dapat membuat akun super admin baru/kepsek, dimana cuma ada 1 superadmin yang bisa daftar.')) {
                    window.location.href = 'delete_superadmin.php?username=" . $username . "';
                }
            </script>";
        } else {
            $error_message = "Jawaban pertanyaan keamanan salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kepala Sekolah - SDN 11 Taluak</title>
    <link href="sb-admin/css/styles.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="sb-admin/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>
    <style>
        .table-container {
            overflow-x: auto;
        }
        .custom-pagination,
        .custom-search,
        .custom-entries {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .custom-pagination {
            justify-content: center;
            margin-top: 1rem;
        }
        .custom-pagination .page-item {
            margin: 0 2px;
        }
        .custom-pagination .page-link {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            color: #007bff;
            text-decoration: none;
        }
        .custom-pagination .page-item.active .page-link {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
        .custom-pagination .page-link:hover {
            background-color: #e9ecef;
        }
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            display: flex;
            justify-content: space-between;
        }
        .icon-button {
            margin-right: 5px;
            cursor: pointer;
        }
        .photo-column {
            display: flex;
            align-items: center;
        }
        .photo-thumbnail {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }
        .pdf-icon {
            color: red;
            margin-right: 5px;
            cursor: pointer;
        }
        .upload-form {
            margin-bottom: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .card-body {
            padding: 20px;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="superadmin_dashboard.php">Dashboard Kepala Sekolah</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Menu</div>
                        <a class="nav-link" href="superadmin_dashboard.php?view=approve">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Setujui Admin
                        </a>
                        <a class="nav-link" href="superadmin_dashboard.php?view=data">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Lihat Data
                        </a>
                        <a class="nav-link" href="superadmin_dashboard.php?view=delete">
                            <div class="sb-nav-link-icon"><i class="fas fa-trash"></i></div>
                            Hapus Akun
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Masuk sebagai:</div>
                    Kepala Sekolah
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard Kepala Sekolah</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Selamat datang, Kepala Sekolah</li>
                    </ol>
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">Setujui Admin</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="superadmin_dashboard.php?view=approve">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">Lihat Data PPDB</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="superadmin_dashboard.php?view=data">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">Informasi Kepala Sekolah</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="#" id="viewSuperAdminInfo">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="superAdminInfoCard" style="display:none;">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-user me-1"></i>
                                Informasi Kepala Sekolah
                            </div>
                            <div class="card-body">
                                <div class="photo-column">
                                <img src="/website/uploads/<?php echo $superadmin['profile_photo']; ?>" class="photo-thumbnail" alt="Foto Kepala Sekolah">
                                <div>
                                        <p><strong>Nama:</strong> <?php echo $superadmin['full_name']; ?></p>
                                        <p><strong>Email:</strong> <?php echo $superadmin['email']; ?></p>
                                        <p><strong>Username:</strong> <?php echo $superadmin['username']; ?></p>
                                        <p><strong>Telepon:</strong> <?php echo $superadmin['phone']; ?></p>
                                        <p><strong>Alamat:</strong> <?php echo $superadmin['address']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($_GET['view']) && $_GET['view'] == 'approve'): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Admin yang Menunggu Persetujuan
                            </div>
                            <div class="card-body">
                                <div class="custom-entries">
                                    <label>Show 
                                        <select id="approveTable-length" class="form-select form-select-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select> entries
                                    </label>
                                    <label>Search:<input type="search" id="approveTable-search" class="form-control form-control-sm" placeholder=""></label>
                                </div>
                                <div class="table-container">
                                    <table id="approveTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $row['username']; ?></td>
                                                    <td><?php echo $row['email']; ?></td>
                                                    <td>
                                                        <form method="POST" action="superadmin_dashboard.php" style="display: inline;">
                                                            <input type="hidden" name="admin_id" value="<?php echo $row['id']; ?>">
                                                            <button type="submit" name="approve" class="btn btn-success">Setujui</button>
                                                        </form>
                                                        <form method="POST" action="superadmin_dashboard.php" style="display: inline;">
                                                            <input type="hidden" name="admin_id" value="<?php echo $row['id']; ?>">
                                                            <button type="submit" name="reject" class="btn btn-danger">Tolak</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="custom-pagination">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination" id="approveTable-pagination"></ul>
                                    </nav>
                                </div>
                                <div class="dataTables_info" id="approveTable_info" role="status" aria-live="polite"></div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-history me-1"></i>
                                Riwayat Persetujuan Admin
                            </div>
                            <div class="card-body">
                                <h5 class="text-success">Disetujui</h5>
                                <table id="approvedAdmins" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row_approved = $result_approved_admins->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row_approved['username']; ?></td>
                                                <td><?php echo $row_approved['email']; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <h5 class="text-danger">Ditolak</h5>
                                <table id="rejectedAdmins" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row_rejected = $result_rejected_admins->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row_rejected['username']; ?></td>
                                                <td><?php echo $row_rejected['email']; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php elseif (isset($_GET['view']) && $_GET['view'] == 'data'): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Informasi Pengguna
                            </div>
                            <div class="card-body">
                                <div class="custom-entries">
                                    <label>Show 
                                        <select id="userTable-length" class="form-select form-select-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select> entries
                                    </label>
                                    <label>Search:<input type="search" id="userTable-search" class="form-control form-control-sm" placeholder=""></label>
                                </div>
                                <div class="table-container">
                                    <table id="userTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nama Lengkap</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row_users = $result_users->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $row_users['id']; ?></td>
                                                    <td><?php echo $row_users['full_name']; ?></td>
                                                    <td><?php echo $row_users['username']; ?></td>
                                                    <td><?php echo $row_users['email']; ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="custom-pagination">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination" id="userTable-pagination"></ul>
                                    </nav>
                                </div>
                                <div class="dataTables_info" id="userTable_info" role="status" aria-live="polite"></div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Data Excel
                            </div>
                            <div class="card-body">
                                <div class="custom-entries">
                                    <label>Show 
                                        <select id="excelTable-length" class="form-select form-select-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select> entries
                                    </label>
                                    <label>Search:<input type="search" id="excelTable-search" class="form-control form-control-sm" placeholder=""></label>
                                </div>
                                <div class="table-container">
                                    <table id="excelTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Nama Lengkap</th>
                                                <th>Jenis Kelamin</th>
                                                <th>NISN</th>
                                                <th>NIK</th>
                                                <th>No. KK</th>
                                                <th>Tempat Lahir</th>
                                                <th>Tanggal Lahir</th>
                                                <th>No. Reg Akta Lahir</th>
                                                <th>Agama</th>
                                                <th>Kewarganegaraan</th>
                                                <th>Berkebutuhan Khusus</th>
                                                <th>Alamat Rumah</th>
                                                <th>RT/RW</th>
                                                <th>Nama Dusun/Jorong</th>
                                                <th>Nama Kelurahan/Desa</th>
                                                <th>Kecamatan</th>
                                                <th>Kode Pos</th>
                                                <th>Lintang/Bujur</th>
                                                <th>Tempat Tinggal Bersama</th>
                                                <th>Anak ke berapa di KK</th>
                                                <th>Transportasi Kesekolah</th>
                                                <th>Penerima KPS/PKH</th>
                                                <th>Punya KIP</th>
                                                <th>Nama Ayah Kandung</th>
                                                <th>NIK Ayah</th>
                                                <th>Tempat Lahir Ayah</th>
                                                <th>Tanggal Lahir Ayah</th>
                                                <th>Pendidikan Ayah</th>
                                                <th>Pekerjaan Ayah</th>
                                                <th>Penghasilan Bulanan Ayah</th>
                                                <th>Nama Ibu Kandung</th>
                                                <th>NIK Ibu</th>
                                                <th>Tempat Lahir Ibu</th>
                                                <th>Tanggal Lahir Ibu</th>
                                                <th>Pendidikan Ibu</th>
                                                <th>Pekerjaan Ibu</th>
                                                <th>Penghasilan Bulanan Ibu</th>
                                                <th>Punya Wali</th>
                                                <th>Nama Wali</th>
                                                <th>NIK Wali</th>
                                                <th>Tempat Lahir Wali</th>
                                                <th>Tanggal Lahir Wali</th>
                                                <th>Pendidikan Wali</th>
                                                <th>Pekerjaan Wali</th>
                                                <th>Penghasilan Bulanan Wali</th>
                                                <th>Nomor Telepon Rumah</th>
                                                <th>No. HP</th>
                                                <th>Email</th>
                                                <th>Tinggi Badan</th>
                                                <th>Berat Badan</th>
                                                <th>Lingkar Kepala</th>
                                                <th>Jarak Tempat Tinggal ke Sekolah</th>
                                                <th>Waktu Tempuh ke Sekolah</th>
                                                <th>Jumlah Saudara Kandung</th>
                                                <th>Jenis Pendaftaran</th>
                                                <th>NIS/NIPD</th>
                                                <th>Sekolah Asal</th>
                                                <th>Pernah Paud Formal (TK)</th>
                                                <th>Pernah Paud Non Formal (KB/TPA/SPS)</th>
                                                <th>Hobi</th>
                                                <th>Cita-Cita</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row_excel = $result_excel->fetch_assoc()): ?>
                                                <tr>
                                                    <td><i class="fas fa-file-pdf pdf-icon" data-id="<?php echo $row_excel['id']; ?>" data-name="<?php echo $row_excel['nama_lengkap']; ?>" data-nis="<?php echo $row_excel['nisn']; ?>" data-nik="<?php echo $row_excel['nik']; ?>"></i></td>
                                                    <td><?php echo $row_excel['nama_lengkap']; ?></td>
                                                    <td><?php echo $row_excel['jenis_kelamin']; ?></td>
                                                    <td><?php echo $row_excel['nisn']; ?></td>
                                                    <td><?php echo $row_excel['nik']; ?></td>
                                                    <td><?php echo $row_excel['no_kk']; ?></td>
                                                    <td><?php echo $row_excel['tempat_lahir']; ?></td>
                                                    <td><?php echo $row_excel['tanggal_lahir']; ?></td>
                                                    <td><?php echo $row_excel['no_reg_akta_lahir']; ?></td>
                                                    <td><?php echo $row_excel['agama']; ?></td>
                                                    <td><?php echo $row_excel['kewarganegaraan']; ?></td>
                                                    <td><?php echo $row_excel['berkebutuhan_khusus']; ?></td>
                                                    <td><?php echo $row_excel['alamat_rumah']; ?></td>
                                                    <td><?php echo $row_excel['rt_rw']; ?></td>
                                                    <td><?php echo $row_excel['nama_dusun_jorong']; ?></td>
                                                    <td><?php echo $row_excel['nama_kelurahan_desa']; ?></td>
                                                    <td><?php echo $row_excel['kecamatan']; ?></td>
                                                    <td><?php echo $row_excel['kode_pos']; ?></td>
                                                    <td><?php echo $row_excel['lintang_bujur']; ?></td>
                                                    <td><?php echo $row_excel['tempat_tinggal_bersama']; ?></td>
                                                    <td><?php echo $row_excel['anak_ke_berapa_di_kk']; ?></td>
                                                    <td><?php echo $row_excel['transportasi_ke_sekolah']; ?></td>
                                                    <td><?php echo $row_excel['penerima_kps_pkh']; ?></td>
                                                    <td><?php echo $row_excel['punya_kip']; ?></td>
                                                    <td><?php echo $row_excel['nama_ayah_kandung']; ?></td>
                                                    <td><?php echo $row_excel['nik_ayah']; ?></td>
                                                    <td><?php echo $row_excel['tempat_lahir_ayah']; ?></td>
                                                    <td><?php echo $row_excel['tanggal_lahir_ayah']; ?></td>
                                                    <td><?php echo $row_excel['pendidikan_ayah']; ?></td>
                                                    <td><?php echo $row_excel['pekerjaan_ayah']; ?></td>
                                                    <td><?php echo $row_excel['penghasilan_bulanan_ayah']; ?></td>
                                                    <td><?php echo $row_excel['nama_ibu_kandung']; ?></td>
                                                    <td><?php echo $row_excel['nik_ibu']; ?></td>
                                                    <td><?php echo $row_excel['tempat_lahir_ibu']; ?></td>
                                                    <td><?php echo $row_excel['tanggal_lahir_ibu']; ?></td>
                                                    <td><?php echo $row_excel['pendidikan_ibu']; ?></td>
                                                    <td><?php echo $row_excel['pekerjaan_ibu']; ?></td>
                                                    <td><?php echo $row_excel['penghasilan_bulanan_ibu']; ?></td>
                                                    <td><?php echo $row_excel['punya_wali']; ?></td>
                                                    <td><?php echo $row_excel['nama_wali']; ?></td>
                                                    <td><?php echo $row_excel['nik_wali']; ?></td>
                                                    <td><?php echo $row_excel['tempat_lahir_wali']; ?></td>
                                                    <td><?php echo $row_excel['tanggal_lahir_wali']; ?></td>
                                                    <td><?php echo $row_excel['pendidikan_wali']; ?></td>
                                                    <td><?php echo $row_excel['pekerjaan_wali']; ?></td>
                                                    <td><?php echo $row_excel['penghasilan_bulanan_wali']; ?></td>
                                                    <td><?php echo $row_excel['nomor_telepon_rumah']; ?></td>
                                                    <td><?php echo $row_excel['no_hp']; ?></td>
                                                    <td><?php echo $row_excel['email']; ?></td>
                                                    <td><?php echo $row_excel['tinggi_badan']; ?></td>
                                                    <td><?php echo $row_excel['berat_badan']; ?></td>
                                                    <td><?php echo $row_excel['lingkar_kepala']; ?></td>
                                                    <td><?php echo $row_excel['jarak_tempat_tinggal_ke_sekolah']; ?></td>
                                                    <td><?php echo $row_excel['waktu_tempuh_ke_sekolah']; ?></td>
                                                    <td><?php echo $row_excel['jumlah_saudara_kandung']; ?></td>
                                                    <td><?php echo $row_excel['jenis_pendaftaran']; ?></td>
                                                    <td><?php echo $row_excel['nis_nipd']; ?></td>
                                                    <td><?php echo $row_excel['sekolah_asal']; ?></td>
                                                    <td><?php echo $row_excel['pernah_paud_formal']; ?></td>
                                                    <td><?php echo $row_excel['pernah_paud_non_formal']; ?></td>
                                                    <td><?php echo $row_excel['hobi']; ?></td>
                                                    <td><?php echo $row_excel['cita_cita']; ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="custom-pagination">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination" id="excelTable-pagination"></ul>
                                    </nav>
                                </div>
                                <div class="dataTables_info" id="excelTable_info" role="status" aria-live="polite"></div>
                            </div>
                        </div>
                    <?php elseif (isset($_GET['view']) && $_GET['view'] == 'delete'): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-trash me-1"></i>
                                Hapus Akun Kepala Sekolah
                            </div>
                            <div class="card-body">
                                <?php if (isset($error_message)): ?>
                                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                <?php endif; ?>
                                <form method="POST" action="superadmin_dashboard.php" id="deleteSuperadminForm">
                                    <div class="mb-3">
                                        <label for="securityAnswer" class="form-label">Jawaban Pertanyaan Keamanan</label>
                                        <input type="text" class="form-control" id="securityAnswer" name="security_answer" required>
                                    </div>
                                    <button type="submit" name="delete_superadmin" class="btn btn-danger">Hapus Akun</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
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

    <script>
        $(document).ready(function() {
            function createPagination(id, table) {
                const wrapper = $('#' + id);
                wrapper.empty();
                const info = table.page.info();

                if (info.pages <= 1) {
                    return;
                }

                const ul = $('<ul class="pagination"></ul>');

                if (info.page > 0) {
                    ul.append('<li class="page-item"><a class="page-link" href="#" data-dt-page="' + (info.page - 1) + '">Previous</a></li>');
                }

                for (let i = 0; i < info.pages; i++) {
                    const active = info.page === i ? ' active' : '';
                    ul.append('<li class="page-item' + active + '"><a class="page-link" href="#" data-dt-page="' + i + '">' + (i + 1) + '</a></li>');
                }

                if (info.page < info.pages - 1) {
                    ul.append('<li class="page-item"><a class="page-link" href="#" data-dt-page="' + (info.page + 1) + '">Next</a></li>');
                }

                wrapper.append(ul);

                wrapper.find('a').click(function (e) {
                    e.preventDefault();
                    const page = $(this).data('dt-page');
                    table.page(page).draw('page');
                });
            }

            const userTable = $('#userTable').DataTable({
                "pagingType": "simple_numbers",
                "responsive": true,
                "dom": 't',
                "drawCallback": function (settings) {
                    createPagination('userTable-pagination', this.api());
                }
            });

            const excelTable = $('#excelTable').DataTable({
                "pagingType": "simple_numbers",
                "responsive": true,
                "dom": 't',
                "drawCallback": function (settings) {
                    createPagination('excelTable-pagination', this.api());
                }
            });

            const approveTable = $('#approveTable').DataTable({
                "pagingType": "simple_numbers",
                "responsive": true,
                "dom": 't',
                "drawCallback": function (settings) {
                    createPagination('approveTable-pagination', this.api());
                }
            });

            $('#userTable-search').on('keyup', function () {
                userTable.search(this.value).draw();
            });

            $('#excelTable-search').on('keyup', function () {
                excelTable.search(this.value).draw();
            });

            $('#approveTable-search').on('keyup', function () {
                approveTable.search(this.value).draw();
            });

            $('#userTable-length').on('change', function () {
                userTable.page.len(this.value).draw();
            });

            $('#excelTable-length').on('change', function () {
                excelTable.page.len(this.value).draw();
            });

            $('#approveTable-length').on('change', function () {
                approveTable.page.len(this.value).draw();
            });

            $('#sidebarToggle').on('click', function() {
                $('body').toggleClass('sb-sidenav-toggled');
            });

            $('#viewSuperAdminInfo').on('click', function(e) {
                e.preventDefault();
                $('#superAdminInfoCard').toggle();
            });

            $(document).on('click', '.fa-eye', function() {
                const imgSrc = $(this).data('img');
                const img = new Image();
                img.src = imgSrc;
                const viewer = window.open("", "Image", "width=600,height=400");
                viewer.document.write(img.outerHTML);
            });

            $(document).on('click', '.pdf-icon', function() {
                const row = $(this).closest('tr');
                const data = {
                    nama_lengkap: row.find('td:eq(1)').text(),
                    jenis_kelamin: row.find('td:eq(2)').text(),
                    nisn: row.find('td:eq(3)').text(),
                    nik: row.find('td:eq(4)').text(),
                    no_kk: row.find('td:eq(5)').text(),
                    tempat_lahir: row.find('td:eq(6)').text(),
                    tanggal_lahir: row.find('td:eq(7)').text(),
                    no_reg_akta_lahir: row.find('td:eq(8)').text(),
                    agama: row.find('td:eq(9)').text(),
                    kewarganegaraan: row.find('td:eq(10)').text(),
                    berkebutuhan_khusus: row.find('td:eq(11)').text(),
                    alamat_rumah: row.find('td:eq(12)').text(),
                    rt_rw: row.find('td:eq(13)').text(),
                    nama_dusun_jorong: row.find('td:eq(14)').text(),
                    nama_kelurahan_desa: row.find('td:eq(15)').text(),
                    kecamatan: row.find('td:eq(16)').text(),
                    kode_pos: row.find('td:eq(17)').text(),
                    lintang_bujur: row.find('td:eq(18)').text(),
                    tempat_tinggal_bersama: row.find('td:eq(19)').text(),
                    anak_ke_berapa_di_kk: row.find('td:eq(20)').text(),
                    transportasi_ke_sekolah: row.find('td:eq(21)').text(),
                    penerima_kps_pkh: row.find('td:eq(22)').text(),
                    punya_kip: row.find('td:eq(23)').text(),
                    nama_ayah_kandung: row.find('td:eq(24)').text(),
                    nik_ayah: row.find('td:eq(25)').text(),
                    tempat_lahir_ayah: row.find('td:eq(26)').text(),
                    tanggal_lahir_ayah: row.find('td:eq(27)').text(),
                    pendidikan_ayah: row.find('td:eq(28)').text(),
                    pekerjaan_ayah: row.find('td:eq(29)').text(),
                    penghasilan_bulanan_ayah: row.find('td:eq(30)').text(),
                    nama_ibu_kandung: row.find('td:eq(31)').text(),
                    nik_ibu: row.find('td:eq(32)').text(),
                    tempat_lahir_ibu: row.find('td:eq(33)').text(),
                    tanggal_lahir_ibu: row.find('td:eq(34)').text(),
                    pendidikan_ibu: row.find('td:eq(35)').text(),
                    pekerjaan_ibu: row.find('td:eq(36)').text(),
                    penghasilan_bulanan_ibu: row.find('td:eq(37)').text(),
                    punya_wali: row.find('td:eq(38)').text(),
                    nama_wali: row.find('td:eq(39)').text(),
                    nik_wali: row.find('td:eq(40)').text(),
                    tempat_lahir_wali: row.find('td:eq(41)').text(),
                    tanggal_lahir_wali: row.find('td:eq(42)').text(),
                    pendidikan_wali: row.find('td:eq(43)').text(),
                    pekerjaan_wali: row.find('td:eq(44)').text(),
                    penghasilan_bulanan_wali: row.find('td:eq(45)').text(),
                    nomor_telepon_rumah: row.find('td:eq(46)').text(),
                    no_hp: row.find('td:eq(47)').text(),
                    email: row.find('td:eq(48)').text(),
                    tinggi_badan: row.find('td:eq(49)').text(),
                    berat_badan: row.find('td:eq(50)').text(),
                    lingkar_kepala: row.find('td:eq(51)').text(),
                    jarak_tempat_tinggal_ke_sekolah: row.find('td:eq(52)').text(),
                    waktu_tempuh_ke_sekolah: row.find('td:eq(53)').text(),
                    jumlah_saudara_kandung: row.find('td:eq(54)').text(),
                    jenis_pendaftaran: row.find('td:eq(55)').text(),
                    nis_nipd: row.find('td:eq(56)').text(),
                    sekolah_asal: row.find('td:eq(57)').text(),
                    pernah_paud_formal: row.find('td:eq(58)').text(),
                    pernah_paud_non_formal: row.find('td:eq(59)').text(),
                    hobi: row.find('td:eq(60)').text(),
                    cita_cita: row.find('td:eq(61)').text()
                };
                generatePDF(data);
            });

            function generatePDF(data) {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('p', 'mm', 'a4');

                // Header
                doc.setFontSize(18);
                doc.text("UPT WEBSITE SDN 11 TALUAK", 105, 15, null, null, 'center');
                doc.setFontSize(14);
                doc.text("Laporan Data Siswa", 105, 25, null, null, 'center');
                doc.setFontSize(12);
                doc.text(`Nama: ${data.nama_lengkap}`, 14, 35);

                // Data Siswa
                const tableColumn = ["Field", "Value"];
                const tableRows = [
                    ["Nama Lengkap", data.nama_lengkap],
                    ["Jenis Kelamin", data.jenis_kelamin],
                    ["NISN", data.nisn],
                    ["NIK", data.nik],
                    ["No. KK", data.no_kk],
                    ["Tempat Lahir", data.tempat_lahir],
                    ["Tanggal Lahir", data.tanggal_lahir],
                    ["No. Reg Akta Lahir", data.no_reg_akta_lahir],
                    ["Agama", data.agama],
                    ["Kewarganegaraan", data.kewarganegaraan],
                    ["Berkebutuhan Khusus", data.berkebutuhan_khusus],
                    ["Alamat Rumah", data.alamat_rumah],
                    ["RT/RW", data.rt_rw],
                    ["Nama Dusun/Jorong", data.nama_dusun_jorong],
                    ["Nama Kelurahan/Desa", data.nama_kelurahan_desa],
                    ["Kecamatan", data.kecamatan],
                    ["Kode Pos", data.kode_pos],
                    ["Lintang/Bujur", data.lintang_bujur],
                    ["Tempat Tinggal Bersama", data.tempat_tinggal_bersama],
                    ["Anak ke Berapa di KK", data.anak_ke_berapa_di_kk],
                    ["Transportasi ke Sekolah", data.transportasi_ke_sekolah],
                    ["Penerima KPS/PKH", data.penerima_kps_pkh],
                    ["Punya KIP", data.punya_kip],
                    ["Nama Ayah Kandung", data.nama_ayah_kandung],
                    ["NIK Ayah", data.nik_ayah],
                    ["Tempat Lahir Ayah", data.tempat_lahir_ayah],
                    ["Tanggal Lahir Ayah", data.tanggal_lahir_ayah],
                    ["Pendidikan Ayah", data.pendidikan_ayah],
                    ["Pekerjaan Ayah", data.pekerjaan_ayah],
                    ["Penghasilan Bulanan Ayah", data.penghasilan_bulanan_ayah],
                    ["Nama Ibu Kandung", data.nama_ibu_kandung],
                    ["NIK Ibu", data.nik_ibu],
                    ["Tempat Lahir Ibu", data.tempat_lahir_ibu],
                    ["Tanggal Lahir Ibu", data.tanggal_lahir_ibu],
                    ["Pendidikan Ibu", data.pendidikan_ibu],
                    ["Pekerjaan Ibu", data.pekerjaan_ibu],
                    ["Penghasilan Bulanan Ibu", data.penghasilan_bulanan_ibu],
                    ["Punya Wali", data.punya_wali],
                    ["Nama Wali", data.nama_wali],
                    ["NIK Wali", data.nik_wali],
                    ["Tempat Lahir Wali", data.tempat_lahir_wali],
                    ["Tanggal Lahir Wali", data.tanggal_lahir_wali],
                    ["Pendidikan Wali", data.pendidikan_wali],
                    ["Pekerjaan Wali", data.pekerjaan_wali],
                    ["Penghasilan Bulanan Wali", data.penghasilan_bulanan_wali],
                    ["Nomor Telepon Rumah", data.nomor_telepon_rumah],
                    ["No. HP", data.no_hp],
                    ["Email", data.email],
                    ["Tinggi Badan", data.tinggi_badan],
                    ["Berat Badan", data.berat_badan],
                    ["Lingkar Kepala", data.lingkar_kepala],
                    ["Jarak Tempat Tinggal ke Sekolah", data.jarak_tempat_tinggal_ke_sekolah],
                    ["Waktu Tempuh ke Sekolah", data.waktu_tempuh_ke_sekolah],
                    ["Jumlah Saudara Kandung", data.jumlah_saudara_kandung],
                    ["Jenis Pendaftaran", data.jenis_pendaftaran],
                    ["NIS/NIPD", data.nis_nipd],
                    ["Sekolah Asal", data.sekolah_asal],
                    ["Pernah Paud Formal (TK)", data.pernah_paud_formal],
                    ["Pernah Paud Non Formal (KB/TPA/SPS)", data.pernah_paud_non_formal],
                    ["Hobi", data.hobi],
                    ["Cita-Cita", data.cita_cita]
                ];

                doc.autoTable({
                    startY: 40,
                    head: [['Keterangan', 'Data']],
                    body: tableRows,
                    theme: 'grid',
                    styles: {
                        fontSize: 10,
                        cellPadding: 3,
                        overflow: 'linebreak'
                    },
                    headStyles: {
                        fillColor: [22, 160, 133],
                        textColor: [255, 255, 255]
                    },
                    columnStyles: {
                        0: { cellWidth: 50 },
                        1: { cellWidth: 130 }
                    },
                    margin: { top: 30 }
                });

                // Tanda Tangan
                const tanggal = new Date().toLocaleDateString('id-ID', {
                    day: 'numeric', month: 'long', year: 'numeric'
                });
                doc.text(`Taluak, ${tanggal}`, 14, doc.autoTable.previous.finalY + 10);
                doc.text('Kepala Sekolah,', 14, doc.autoTable.previous.finalY + 20);
                doc.text('________________________', 14, doc.autoTable.previous.finalY + 50);
                doc.text('Nama Kepala Sekolah', 14, doc.autoTable.previous.finalY + 60);

                // Save the PDF
                doc.save(`${data.nama_lengkap}-${data.nisn}-${data.nik}.pdf`);
            }

            $('#deleteSuperadminForm').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Jika Anda menghapus akun ini, Anda dapat membuat akun baru untuk super admin atau kepala sekolah. Hanya satu akun kepala sekolah yang diperbolehkan untuk mendaftar pada sistem ini",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
