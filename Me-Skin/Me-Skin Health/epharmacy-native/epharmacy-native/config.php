<?php
// config.php

// Pengaturan untuk menampilkan error (berguna saat development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Konfigurasi Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "appointment_db";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Memulai session di satu tempat terpusat
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>