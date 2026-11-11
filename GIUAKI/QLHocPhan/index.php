<?php
require_once 'config.php';

$sql = "SELECT * FROM GiangVien ORDER BY IDGV ASC";
$result = $conn->query($sql);
$total = ($result) ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Giảng Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f5f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
        }
        .page-header {
            background: #ffffff;
            border-radius: 10px;
            padding: 12px 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border-left: 4px solid #0d6efd;
        }
       
        .gv-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .gv-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        }
       
        .gv-img {
            width: 100%;
            height: 165px;
            object-fit: cover;
            object-position: center 15%;
            border-bottom: 1px solid #f1f3f5;
        }
    
        .gv-body {
            padding: 10px;
            text-align: center;
        }
        .gv-id {
            display: inline-block;
            background-color: #e7f1ff;
            color: #0d6efd;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
            margin-bottom: 4px;
        }
        .gv-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .gv-class {
            font-size: 0.78rem;
            color: #64748b;
            background: #f8f9fa;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 8px;
        }
        .btn-action {
            font-size: 0.75rem;
            padding: 3px 6px;
        }
    </style>
</head>
<body>

<div class="container-xxl py-4">
    <!-- Tiêu đề & Nút thêm -->
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-graduation-cap text-primary me-2"></i>Danh Sách Giảng Viên
            </h5>
            <span class="text-muted" style="font-size: 0.82rem;">Tổng số hiện có: <strong><?php echo $total; ?></strong> giảng viên</span>
        </div>
        <a href="add.php" class="btn btn-primary btn-sm px-3 py-2 fw-semibold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Thêm Giảng Viên
        </a>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show py-2 small shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-1"></i>
            <?php
                if ($_GET['msg'] == 'added') echo "Đã thêm giảng viên thành công.";
                elseif ($_GET['msg'] == 'updated') echo "Đã cập nhật thông tin giảng viên.";
                elseif ($_GET['msg'] == 'deleted') echo "Đã xóa giảng viên.";
            ?>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Danh sách 6 Giảng viên / 1 hàng trên màn hình lớn (col-lg-2) -->
    <div class="row g-3">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($gv = $result->fetch_assoc()): ?>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="gv-card h-100 d-flex flex-column">
                        <img src="<?php echo htmlspecialchars($gv['Image']); ?>" class="gv-img" alt="Ảnh giảng viên">
                        <div class="gv-body d-flex flex-column flex-grow-1">
                            <div>
                                <span class="gv-id">Mã GV: <?php echo htmlspecialchars($gv['IDGV']); ?></span>
                                <div class="gv-name" title="<?php echo htmlspecialchars($gv['Fullname']); ?>">
                                    <?php echo htmlspecialchars($gv['Fullname']); ?>
                                </div>
                                <div class="gv-class">
                                    Tổng số lớp: <strong class="text-dark"><?php echo (int)$gv['SumClass']; ?></strong>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-1 mt-auto pt-2 border-top">
                                <a href="edit.php?id=<?php echo urlencode($gv['IDGV']); ?>" class="btn btn-outline-primary btn-action w-50">
                                    <i class="fa-regular fa-pen-to-square"></i> Sửa
                                </a>
                                <a href="delete.php?id=<?php echo urlencode($gv['IDGV']); ?>" 
                                   class="btn btn-outline-danger btn-action w-50"
                                   onclick="return confirm('Xóa giảng viên <?php echo htmlspecialchars($gv['Fullname']); ?>?');">
                                    <i class="fa-regular fa-trash-can"></i> Xóa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Chưa có dữ liệu giảng viên.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>