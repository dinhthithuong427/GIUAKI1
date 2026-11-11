<?php
$host = "localhost";
$username = "root";
$password = ""; 
$dbname = "QLHocPhan";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Tự động tạo thư mục uploads nếu chưa tồn tại
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}
?>