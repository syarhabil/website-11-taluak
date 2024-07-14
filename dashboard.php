<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include('config.php');

$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE nik='$username' OR nisn='$username'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link href="sb-admin/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .complete-registration-section {
            margin-top: 30px;
            text-align: center;
        }
        .complete-registration-section h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        .complete-registration .btn-complete {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .complete-registration .btn-complete:hover {
            background-color: #0056b3;
        }
        @media (max-width: 576px) {
            .complete-registration .btn-complete {
                width: 100%;
                font-size: 14px;
            }
        }
        /* Notification circle */

        .card-selesaikan-pendaftaran {
            background-color: #007bff;
            color: white;
            position: relative; /* Add this line if not already present */
        }
        .card-selesaikan-pendaftaran::after {
            content: '';
            position: absolute;
            top: 10px;
            right: 10px;
            width: 15px;
            height: 15px;
            background-color: red;
            border-radius: 50%;
            animation: blink 1s infinite;
            z-index: 10; /* Ensure it appears on top */
        }

        @keyframes blink {
            50% {
                opacity: 0;
            }
        }

    </style>
</head>
<body id="page-top">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="dashboard.php">SD 11 Taluak</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
         <!-- Navbar Search-->
         <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <!-- <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div> -->
        </form>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="edit_account.php">Edit Akun</a></li>
                    <li><a class="dropdown-item" id="logout-link" href="#">Logout</a></li>
                    <li><a class="dropdown-item" id="delete-account-link" href="#">Hapus Akun</a></li>
                </ul>
            </li>
        </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Interface</div>
                        <a class="nav-link" href="edit_account.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-edit"></i></div>
                            Edit Akun
                        </a>
                        <a class="nav-link" href="https://forms.gle/VYNoRzACfoV2AK7JA">
                            <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                            Selesaikan Pendaftaran
                        </a>
                        <a class="nav-link" id="logout-link-side" href="#">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                            Logout
                        </a>
                        <a class="nav-link" id="delete-account-link-side" href="#">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-times"></i></div>
                            Hapus Akun
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php echo $user['full_name']; ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                    <div class="row">
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">Informasi Siswa</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="#">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card card-selesaikan-pendaftaran text-white mb-4">
                                <div class="card-body">Selesaikan Pendaftaran</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="https://forms.gle/VYNoRzACfoV2AK7JA">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">Akun</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="edit_account.php">Edit Akun</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="complete-registration-section">
                        <h3>Lengkapi Pendaftaran Anda</h3>
                        <p>Silakan klik tombol di bawah ini untuk menyelesaikan proses pendaftaran.</p>
                        <div class="complete-registration">
                            <a href="https://forms.gle/VYNoRzACfoV2AK7JA" class="btn btn-complete">Selesaikan Pendaftaran</a>
                        </div>
                    </div>
                    <p></p>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Informasi Siswa
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">Full Name: <?php echo $user['full_name']; ?></li>
                                <li class="list-group-item">NIK: <?php echo $user['nik']; ?></li>
                                <li class="list-group-item">NISN: <?php echo $user['nisn']; ?></li>
                                <li class="list-group-item">Phone: <?php echo $user['phone']; ?></li>
                                <li class="list-group-item">Graduation Year: <?php echo $user['grad_year']; ?></li>
                                <li class="list-group-item"><img src="uploads/<?php echo $user['photo']; ?>" alt="Foto" width="150"></li>
                            </ul>
                        </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="sb-admin/js/scripts.js"></script>
    <script>
        document.getElementById('logout-link').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan logout!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, logout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout_user.php';
                }
            })
        });

        document.getElementById('delete-account-link').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus akun ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus akun!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_account.php';
                }
            })
        });

        document.getElementById('logout-link-side').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan logout!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, logout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout_user.php';
                }
            })
        });

        document.getElementById('delete-account-link-side').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus akun ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus akun!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_account.php';
                }
            })
        });
    </script>
</body>
</html>
