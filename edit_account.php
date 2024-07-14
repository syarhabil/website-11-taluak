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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $nik = $_POST['nik'];
    $nisn = $_POST['nisn'];
    $phone = $_POST['phone'];
    $grad_year = $_POST['grad_year'];
    $photo = $_FILES['photo']['name'];
    $photo_tmp = $_FILES['photo']['tmp_name'];

    if (!empty($photo)) {
        move_uploaded_file($photo_tmp, "uploads/" . $photo);
        $sql = "UPDATE users SET full_name='$full_name', nik='$nik', nisn='$nisn', phone='$phone', grad_year='$grad_year', photo='$photo' WHERE nik='$username' OR nisn='$username'";
    } else {
        $sql = "UPDATE users SET full_name='$full_name', nik='$nik', nisn='$nisn', phone='$phone', grad_year='$grad_year' WHERE nik='$username' OR nisn='$username'";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Akun - SB Admin</title>
    <link href="sb-admin/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <h1 class="mt-4">Edit Akun</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit Akun</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user-edit me-1"></i>
                            Edit Akun
                        </div>
                        <div class="card-body">
                            <form action="edit_account.php" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" class="form-control" id="nik" name="nik" value="<?php echo $user['nik']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="nisn" class="form-label">NISN</label>
                                    <input type="text" class="form-control" id="nisn" name="nisn" value="<?php echo $user['nisn']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="grad_year" class="form-label">Graduation Year</label>
                                    <input type="text" class="form-control" id="grad_year" name="grad_year" value="<?php echo $user['grad_year']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Foto</label>
                                    <input type="file" class="form-control" id="photo" name="photo">
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
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
