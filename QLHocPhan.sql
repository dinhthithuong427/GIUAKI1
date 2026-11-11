CREATE DATABASE IF NOT EXISTS QLHocPhan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE QLHocPhan;

CREATE TABLE IF NOT EXISTS GiangVien (
    IDGV VARCHAR(20) PRIMARY KEY,
    Fullname VARCHAR(100) NOT NULL,
    Image VARCHAR(255) DEFAULT NULL,
    SumClass INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu
INSERT INTO GiangVien (IDGV, Fullname, Image, SumClass) VALUES
('GV001', 'Nguyễn Văn An', 'https://ui-avatars.com/api/?name=Nguyen+Van+An&background=0D8ABC&color=fff&size=200', 5),
('GV002', 'Trần Thị Bích Ngọc', 'https://ui-avatars.com/api/?name=Tran+Thi+Bich+Ngoc&background=E91E63&color=fff&size=200', 4),
('GV003', 'Lê Hoàng Nam', 'https://ui-avatars.com/api/?name=Le+Hoang+Nam&background=2E7D32&color=fff&size=200', 6);