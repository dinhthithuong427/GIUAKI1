<?php
require_once 'config.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idgv = $_GET['id'];

    // Lấy đường dẫn ảnh để xóa file vật lý trong thư mục uploads (nếu có)
    $stmt = $conn->prepare("SELECT Image FROM GiangVien WHERE IDGV = ?");
    $stmt->bind_param("s", $idgv);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if (!empty($row['Image']) && file_exists($row['Image']) && strpos($row['Image'], 'uploads/') === 0) {
            unlink($row['Image']);
        }
    }
    $stmt->close();

    // Xóa bản ghi trong CSDL
    $delStmt = $conn->prepare("DELETE FROM GiangVien WHERE IDGV = ?");
    $delStmt->bind_param("s", $idgv);
    $delStmt->execute();
    $delStmt->close();
}

header("Location: index.php?msg=deleted");
exit();
?>