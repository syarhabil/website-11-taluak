<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == UPLOAD_ERR_OK) {
        require 'vendor/autoload.php';
        
        $file = $_FILES['excel_file']['tmp_name'];
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
        $spreadsheet = $reader->load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $columns = [
            'nama_lengkap', 'jenis_kelamin', 'nisn', 'nik', 'no_kk', 
            'tempat_lahir', 'tanggal_lahir', 'no_reg_akta_lahir', 'agama', 'kewarganegaraan', 
            'berkebutuhan_khusus', 'alamat_rumah', 'rt_rw', 'nama_dusun_jorong', 'nama_kelurahan_desa', 
            'kecamatan', 'kode_pos', 'lintang_bujur', 'tempat_tinggal_bersama', 'anak_ke_berapa_di_kk', 
            'transportasi_ke_sekolah', 'penerima_kps_pkh', 'punya_kip', 'nama_ayah_kandung', 'nik_ayah', 
            'tempat_lahir_ayah', 'tanggal_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_bulanan_ayah', 
            'nama_ibu_kandung', 'nik_ibu', 'tempat_lahir_ibu', 'tanggal_lahir_ibu', 'pendidikan_ibu', 
            'pekerjaan_ibu', 'penghasilan_bulanan_ibu', 'punya_wali', 'nama_wali', 'nik_wali', 
            'tempat_lahir_wali', 'tanggal_lahir_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_bulanan_wali', 
            'nomor_telepon_rumah', 'no_hp', 'email', 'tinggi_badan', 'berat_badan', 
            'lingkar_kepala', 'jarak_tempat_tinggal_ke_sekolah', 'waktu_tempuh_ke_sekolah', 'jumlah_saudara_kandung', 'jenis_pendaftaran', 
            'nis_nipd', 'sekolah_asal', 'pernah_paud_formal', 'pernah_paud_non_formal', 'hobi', 'cita_cita'
        ];

        $sql = "INSERT INTO excel_data (" . implode(',', $columns) . ") VALUES ";
        $values = [];

        foreach ($sheetData as $key => $row) {
            if ($key == 0) continue; // Skip header row
            $row = array_map(function($value) use ($conn) {
                return "'" . $conn->real_escape_string($value) . "'";
            }, $row);

            // Adjust the row to match the number of columns in the table
            if (count($row) < count($columns)) {
                $row = array_pad($row, count($columns), 'NULL');
            }

            $values[] = "(" . implode(',', $row) . ")";
        }

        if (!empty($values)) {
            $sql .= implode(',', $values);
            if ($conn->query($sql)) {
                echo "Data inserted successfully";
            } else {
                echo "Error: " . $conn->error;
            }
        } else {
            echo "No data to insert";
        }
    } else {
        echo "Error uploading file";
    }
} else {
    echo "Invalid request";
}
?>
