<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

include 'config.php';

if (isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    // Hapus semua data lama
    $delete_sql = "DELETE FROM excel_data";
    mysqli_query($conn, $delete_sql);

    $berhasil = 0;
    foreach ($sheetData as $key => $row) {
        // Lewati header
        if ($key == 1) {
            continue;
        }

        // Tangkap data dan masukkan ke variabel sesuai dengan kolomnya
        $nama_lengkap = $row['B'];
        $jenis_kelamin = $row['C'];
        $nisn = $row['D'];
        $nik = $row['E'];
        $no_kk = $row['F'];
        $tempat_lahir = $row['G'];
        $tanggal_lahir = $row['H'];
        $no_reg_akta_lahir = $row['I'];
        $agama = $row['J'];
        $kewarganegaraan = $row['K'];
        $berkebutuhan_khusus = $row['L'];
        $alamat_rumah = $row['M'];
        $rt_rw = $row['N'];
        $nama_dusun_jorong = $row['O'];
        $nama_kelurahan_desa = $row['P'];
        $kecamatan = $row['Q'];
        $kode_pos = $row['R'];
        $lintang_bujur = $row['S'];
        $tempat_tinggal_bersama = $row['T'];
        $anak_ke_berapa_di_kk = $row['U'];
        $transportasi_ke_sekolah = $row['V'];
        $penerima_kps_pkh = $row['W'];
        $punya_kip = $row['X'];
        $nama_ayah_kandung = $row['Y'];
        $nik_ayah = $row['Z'];
        $tempat_lahir_ayah = $row['AA'];
        $tanggal_lahir_ayah = $row['AB'];
        $pendidikan_ayah = $row['AC'];
        $pekerjaan_ayah = $row['AD'];
        $penghasilan_bulanan_ayah = $row['AE'];
        $nama_ibu_kandung = $row['AF'];
        $nik_ibu = $row['AG'];
        $tempat_lahir_ibu = $row['AH'];
        $tanggal_lahir_ibu = $row['AI'];
        $pendidikan_ibu = $row['AJ'];
        $pekerjaan_ibu = $row['AK'];
        $penghasilan_bulanan_ibu = $row['AL'];
        $punya_wali = $row['AM'];
        $nama_wali = $row['AN'];
        $nik_wali = $row['AO'];
        $tempat_lahir_wali = $row['AP'];
        $tanggal_lahir_wali = $row['AQ'];
        $pendidikan_wali = $row['AR'];
        $pekerjaan_wali = $row['AS'];
        $penghasilan_bulanan_wali = $row['AT'];
        $nomor_telepon_rumah = $row['AU'];
        $no_hp = $row['AV'];
        $email = $row['AW'];
        $tinggi_badan = $row['AX'];
        $berat_badan = $row['AY'];
        $lingkar_kepala = $row['AZ'];
        $jarak_tempat_tinggal_ke_sekolah = $row['BA'];
        $waktu_tempuh_ke_sekolah = $row['BB'];
        $jumlah_saudara_kandung = $row['BC'];
        $jenis_pendaftaran = $row['BD'];
        $nis_nipd = $row['BE'];
        $sekolah_asal = $row['BF'];
        $pernah_paud_formal = $row['BG'];
        $pernah_paud_non_formal = $row['BH'];
        $hobi = $row['BI'];
        $cita_cita = $row['BJ'];

        if($nama_lengkap != "" && $jenis_kelamin != "" && $nisn != ""){
            // input data ke database (table excel_data)
            $sql = "INSERT INTO excel_data (
                nama_lengkap, jenis_kelamin, nisn, nik, no_kk, tempat_lahir, tanggal_lahir, no_reg_akta_lahir, agama, kewarganegaraan, berkebutuhan_khusus, alamat_rumah, rt_rw, nama_dusun_jorong, nama_kelurahan_desa, kecamatan, kode_pos, lintang_bujur, tempat_tinggal_bersama, anak_ke_berapa_di_kk, transportasi_ke_sekolah, penerima_kps_pkh, punya_kip, nama_ayah_kandung, nik_ayah, tempat_lahir_ayah, tanggal_lahir_ayah, pendidikan_ayah, pekerjaan_ayah, penghasilan_bulanan_ayah, nama_ibu_kandung, nik_ibu, tempat_lahir_ibu, tanggal_lahir_ibu, pendidikan_ibu, pekerjaan_ibu, penghasilan_bulanan_ibu, punya_wali, nama_wali, nik_wali, tempat_lahir_wali, tanggal_lahir_wali, pendidikan_wali, pekerjaan_wali, penghasilan_bulanan_wali, nomor_telepon_rumah, no_hp, email, tinggi_badan, berat_badan, lingkar_kepala, jarak_tempat_tinggal_ke_sekolah, waktu_tempuh_ke_sekolah, jumlah_saudara_kandung, jenis_pendaftaran, nis_nipd, sekolah_asal, pernah_paud_formal, pernah_paud_non_formal, hobi, cita_cita
            ) VALUES (
                '$nama_lengkap', '$jenis_kelamin', '$nisn', '$nik', '$no_kk', '$tempat_lahir', '$tanggal_lahir', '$no_reg_akta_lahir', '$agama', '$kewarganegaraan', '$berkebutuhan_khusus', '$alamat_rumah', '$rt_rw', '$nama_dusun_jorong', '$nama_kelurahan_desa', '$kecamatan', '$kode_pos', '$lintang_bujur', '$tempat_tinggal_bersama', '$anak_ke_berapa_di_kk', '$transportasi_ke_sekolah', '$penerima_kps_pkh', '$punya_kip', '$nama_ayah_kandung', '$nik_ayah', '$tempat_lahir_ayah', '$tanggal_lahir_ayah', '$pendidikan_ayah', '$pekerjaan_ayah', '$penghasilan_bulanan_ayah', '$nama_ibu_kandung', '$nik_ibu', '$tempat_lahir_ibu', '$tanggal_lahir_ibu', '$pendidikan_ibu', '$pekerjaan_ibu', '$penghasilan_bulanan_ibu', '$punya_wali', '$nama_wali', '$nik_wali', '$tempat_lahir_wali', '$tanggal_lahir_wali', '$pendidikan_wali', '$pekerjaan_wali', '$penghasilan_bulanan_wali', '$nomor_telepon_rumah', '$no_hp', '$email', '$tinggi_badan', '$berat_badan', '$lingkar_kepala', '$jarak_tempat_tinggal_ke_sekolah', '$waktu_tempuh_ke_sekolah', '$jumlah_saudara_kandung', '$jenis_pendaftaran', '$nis_nipd', '$sekolah_asal', '$pernah_paud_formal', '$pernah_paud_non_formal', '$hobi', '$cita_cita'
            )";

            mysqli_query($conn, $sql);
            $berhasil++;
        }
    }

    // Alihkan halaman ke admin_dashboard.php dengan pesan sukses
    header("Location: admin_dashboard.php?berhasil=$berhasil");
}
?>