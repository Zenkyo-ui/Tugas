<?php
require 'config/koneksi.php';

$id = $_GET['id'] ?? null;

if ($id && is_numeric($id)) {
    $stmt = mysqli_prepare($conn, "DELETE FROM absensi WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: index.php");
exit;