<?php
require_once 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$idgv = $_GET['id'];
$error = "";

$stmt = $conn->prepare("SELECT * FROM GiangVien WHERE IDGV = ?");
$stmt->bind_param("s", $idgv);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}
$gv = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['Fullname']);
    $sumClass = (int)$_POST['SumClass'];
    $imagePath = $gv['Image'];

    if (isset($_FILES['Image']) && $_FILES['Image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['Image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newFileName = "gv_" . time() . "_" . rand(100, 999) . "." . $ext;
            $destination = "uploads/" . $newFileName;
            if (move_uploaded_file($_FILES['Image']['tmp_name'], $destination)) {
                $imagePath = $destination;
            } else {
                $error = "Không thể lưu ảnh mới.";
            }
        } else {
            $error = "Chỉ hỗ trợ định dạng ảnh JPG, PNG, GIF, WEBP.";
        }
    }

    if (empty($error)) {
        $updateStmt = $conn->prepare("UPDATE GiangVien SET Fullname = ?, Image = ?, SumClass = ? WHERE IDGV = ?");
        $updateStmt->bind_param("ssis", $fullname, $imagePath, $sumClass, $idgv);

        if ($updateStmt->execute()) {
            header("Location: index.php?msg=updated");
            exit();
        } else {
            $error = "Lỗi cập nhật: " . $conn->error;
        }
        $updateStmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Giảng Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f3f5f8 0%, #e5ebf3 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            padding: 30px 0;
        }
        .form-card {
            max-width: 540px;
            width: 100%;
            margin: 0 auto;
            background: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }
        .form-header {
            background: #ffffff;
            padding: 24px 28px 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .header-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #e7f1ff;
            color: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .form-body {
            padding: 24px 28px 28px;
        }
        .form-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .input-group-text {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #64748b;
            width: 42px;
            justify-content: center;
        }
        .form-control {
            border-color: #e2e8f0;
            padding: 10px 14px;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
        }
        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .upload-zone:hover {
            border-color: #0d6efd;
            background: #f0f7ff;
        }
        #previewImg {
            width: 110px;
            height: 140px;
            object-fit: cover;
            object-position: center 15%;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 0 auto 8px;
            display: block;
            border: 2px solid #fff;
        }
        .btn-submit {
            padding: 10px 24px;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }
        .btn-back {
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <div class="form-header d-flex align-items-center gap-3">
            <div class="header-icon">
                <i class="fa-solid fa-user-pen"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark">Cập Nhật Giảng Viên</h5>
                <small class="text-muted">Chỉnh sửa thông tin giảng viên <strong><?php echo htmlspecialchars($gv['IDGV']); ?></strong></small>
            </div>
        </div>

        <div class="form-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="edit.php?id=<?php echo urlencode($gv['IDGV']); ?>" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-sm-7">
                        <label class="form-label">Mã Giảng viên (IDGV)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-id-badge"></i></span>
                            <input type="text" class="form-control bg-light text-muted fw-semibold" value="<?php echo htmlspecialchars($gv['IDGV']); ?>" readonly>
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <label class="form-label">Tổng số lớp (SumClass) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-layer-group"></i></span>
                            <input type="number" name="SumClass" class="form-control" min="0" max="50" value="<?php echo (int)$gv['SumClass']; ?>" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Họ và tên (Fullname) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-user-tie"></i></span>
                            <input type="text" name="Fullname" class="form-control" value="<?php echo htmlspecialchars($gv['Fullname']); ?>" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Hình ảnh đại diện (Image)</label>
                        <label for="imageInput" class="upload-zone d-block mb-0">
                            <img src="<?php echo htmlspecialchars($gv['Image']); ?>" id="previewImg" alt="Ảnh hiện tại">
                            <div id="fileNameText" class="text-primary small fw-semibold">
                                <i class="fa-solid fa-camera me-1"></i> Nhấn vào khung để đổi ảnh mới
                            </div>
                            <div class="text-muted" style="font-size: 0.76rem;">(Bỏ qua nếu muốn giữ nguyên ảnh hiện tại)</div>
                        </label>
                        <input type="file" name="Image" id="imageInput" class="d-none" accept="image/*">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="index.php" class="btn btn-light border text-secondary btn-back">
                        <i class="fa-solid fa-arrow-left me-1"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-primary btn-submit">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('previewImg').src = URL.createObjectURL(file);
            document.getElementById('fileNameText').textContent = 'Ảnh mới: ' + file.name;
        }
    });
</script>
</body>
</html>