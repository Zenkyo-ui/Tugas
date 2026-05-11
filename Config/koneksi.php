<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "absensi_db"
);

if (!$conn) {
    die("Koneksi database gagal!");
}