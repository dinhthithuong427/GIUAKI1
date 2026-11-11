<?php
require_once 'config.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idgv = trim($_POST['IDGV']);$fullname = trim($_POST['Fullname']);$sumClass = (int)$_POST['SumClass'];$imagePath = "";

    $checkStmt =$conn->prepare("SELECT IDGV FROM GiangVien WHERE IDGV = ?");
    $checkStmt->bind_param("s", $idgv);
    $checkStmt->execute();$checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $error = "Mã giảng viên '$idgv' đã tồn tại! Vui lòng nhập mã khác.";
    } else {
        if (isset($_FILES['Image']) && $_FILES['Image']['error'] == 0) {$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['Image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext,$allowed)) {
                $newFileName = "gv_" . time() . "_" . rand(100, 999) . "." . $ext;
                $destination = "uploads/" . $newFileName;
                if (move_uploaded_file($_FILES['Image']['tmp_name'],$destination)) {
                    $imagePath =$destination;
                } else {
                    $error = "Không thể lưu file ảnh tải lên.";
                }
            } else {
                $error = "Chỉ hỗ trợ định dạng ảnh JPG, PNG, GIF, WEBP.";
            }
        }

        if (empty($error)) {
            $stmt =$conn->prepare("INSERT INTO GiangVien (IDGV, Fullname, Image, SumClass) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $idgv,$fullname, $imagePath,$sumClass);
            if ($stmt->execute()) {
                header("Location: index.php?msg=added");
                exit();
            } else {
                $error = "Lỗi hệ thống: " . $conn->error;
            }
            $stmt->close();
        }
    }
    $checkStmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Giảng Viên Mới</title>
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
        /* Khung chọn ảnh chuyên nghiệp */
        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 18px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
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
            display: none;
            margin: 0 auto 10px;
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
        <!-- Header -->
        <div class="form-header d-flex align-items-center gap-3">
            <div class="header-icon">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark">Thêm Giảng Viên Mới</h5>
                <small class="text-muted">Nhập thông tin hồ sơ giảng viên vào hệ thống</small>
            </div>
        </div>

        <!-- Body -->
        <div class="form-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 small d-flex align-items-center">
                    <i class="fa-solid fa-circle-exclamation me-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="add.php" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <!-- Mã Giảng viên & Tổng số lớp trên cùng 1 hàng cho gọn -->
                    <div class="col-sm-7">
                        <label class="form-label">Mã Giảng viên (IDGV) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-id-badge"></i></span>
                            <input type="text" name="IDGV" class="form-control" placeholder="VD: GV006" required
                                   value="<?php echo isset($_POST['IDGV']) ? htmlspecialchars($_POST['IDGV']) : ''; ?>">
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <label class="form-label">Tổng số lớp (SumClass) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-layer-group"></i></span>
                            <input type="number" name="SumClass" class="form-control" min="0" max="50" value="1" required>
                        </div>
                    </div>

                    <!-- Họ và tên -->
                    <div class="col-12">
                        <label class="form-label">Họ và tên (Fullname) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-user-tie"></i></span>
                            <input type="text" name="Fullname" class="form-control" placeholder="Nhập đầy đủ họ và tên..." required
                                   value="<?php echo isset($_POST['Fullname']) ? htmlspecialchars($_POST['Fullname']) : ''; ?>">
                        </div>
                    </div>

                    <!-- Hình ảnh chân dung -->
                    <div class="col-12">
                        <label class="form-label">Hình ảnh đại diện (Image) <span class="text-danger">*</span></label>
                        <label for="imageInput" class="upload-zone d-block mb-0">
                            <img id="previewImg" alt="Xem trước">
                            <div id="uploadPlaceholder">
                                <i class="fa-solid fa-cloud-arrow-up text-primary fs-3 mb-2"></i>
                                <div class="fw-semibold text-dark small">Nhấn vào đây để chọn ảnh chân dung</div>
                                <div class="text-muted" style="font-size: 0.78rem;">Hỗ trợ JPG, PNG, WEBP (Hiển thị xem trước ngay)</div>
                            </div>
                            <div id="fileNameText" class="text-primary small fw-semibold mt-1"></div>
                        </label>
                        <input type="file" name="Image" id="imageInput" class="d-none" accept="image/*" required>
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="index.php" class="btn btn-light border text-secondary btn-back">
                        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary btn-submit">
                        <i class="fa-solid fa-check me-1"></i> Thêm mới
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('previewImg');
        const placeholder = document.getElementById('uploadPlaceholder');
        const fileNameText = document.getElementById('fileNameText');

        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            fileNameText.textContent = 'Đã chọn: ' + file.name + ' (Nhấn để đổi ảnh khác)';
        }
    });
</script>
</body>
</html>