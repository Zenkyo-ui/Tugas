<?php
require 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']  ?? '');
    $kelas  = trim($_POST['kelas'] ?? '');
    $status = $_POST['status'] ?? 'Hadir';

    $allowed = ['Hadir', 'Izin', 'Sakit', 'Alpa'];
    if (!in_array($status, $allowed)) $status = 'Hadir';

    if ($nama && $kelas) {
        $stmt = mysqli_prepare($conn, "INSERT INTO absensi (nama, kelas, status_kehadiran) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $nama, $kelas, $status);
        mysqli_stmt_execute($stmt);
    }

    header("Location: index.php");
    exit;
}