-- All-in-One Import Script for TuVanSucKhoe
-- Date: Dec 25, 2025
-- Server: MariaDB 10.4.28 | PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database
CREATE DATABASE IF NOT EXISTS `tuvansuckhoe` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `tuvansuckhoe`;

-- =============================================
-- Reset existing tables (fresh import)
-- =============================================
DROP TABLE IF EXISTS `DonThuoc`;
DROP TABLE IF EXISTS `HoSoBenhNhan`;
DROP TABLE IF EXISTS `LichHen`;
DROP TABLE IF EXISTS `ChanDoanAI`;
DROP TABLE IF EXISTS `BenhNhan`;
DROP TABLE IF EXISTS `BacSi`;
DROP TABLE IF EXISTS `tuvan`;
DROP TABLE IF EXISTS `lienhe`;
DROP TABLE IF EXISTS `bacsi`;
DROP TABLE IF EXISTS `khoa`;
DROP TABLE IF EXISTS `nhanvien`;

-- =============================================
-- Core domain tables (Khoa, BacSi, TuVan, LienHe)
-- =============================================

-- KHOA (Specialties)
CREATE TABLE `khoa` (
  `IDKHOA` int(11) NOT NULL AUTO_INCREMENT,
  `TENKHOA` varchar(100) NOT NULL,
  `MOTA` text DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`IDKHOA`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `khoa` (`IDKHOA`, `TENKHOA`, `MOTA`) VALUES
(1, 'TIM MACH', 'Khoa Tim mạch chuyên điều trị các bệnh về tim và mạch máu'),
(2, 'NỘI TIẾT', 'Khoa Nội tiết chuyên điều trị các bệnh về nội tiết tố, đái tháo đường'),
(3, 'NHI KHOA', 'Khoa Nhi chuyên khám và điều trị cho trẻ em'),
(4, 'TAI MŨI HỌNG', 'Khoa Tai Mũi Họng chuyên điều trị các bệnh về tai, mũi, họng'),
(5, 'SẢN PHỤ KHOA', 'Khoa Sản Phụ khoa chuyên khám và điều trị cho phụ nữ'),
(6, 'DA LIỄU', 'Khoa Da liễu chuyên điều trị các bệnh về da'),
(7, 'UNG BƯỚU', 'Khoa Ung bướu chuyên điều trị các bệnh ung thư'),
(8, 'NỘI TỔNG QUÁT', 'Khoa Nội tổng quát điều trị các bệnh nội khoa');

-- BACSI (Doctors)
CREATE TABLE `bacsi` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `HOTEN` varchar(100) NOT NULL,
  `IDKHOA` int(11) DEFAULT NULL,
  `CHUCVU` varchar(100) DEFAULT NULL,
  `KINHNGHIEM` text DEFAULT NULL,
  `EMAIL` varchar(100) DEFAULT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `ANH` varchar(255) DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  KEY `IDKHOA` (`IDKHOA`),
  CONSTRAINT `bacsi_ibfk_1` FOREIGN KEY (`IDKHOA`) REFERENCES `khoa` (`IDKHOA`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bacsi` (`ID`, `HOTEN`, `IDKHOA`, `CHUCVU`, `KINHNGHIEM`, `EMAIL`, `SDT`) VALUES
(1, 'BS. Nguyễn Văn An', 1, 'Bác sĩ chuyên khoa II', '15 năm kinh nghiệm trong lĩnh vực tim mạch', 'bs.an@hospital.vn', '0912345001'),
(2, 'BS. Trần Thị Bình', 2, 'Bác sĩ chuyên khoa I', '10 năm kinh nghiệm điều trị nội tiết', 'bs.binh@hospital.vn', '0912345002'),
(3, 'BS. Lê Văn Cường', 3, 'Bác sĩ chuyên khoa II', '12 năm kinh nghiệm khám trẻ em', 'bs.cuong@hospital.vn', '0912345003'),
(4, 'BS. Phạm Thị Dung', 4, 'Bác sĩ chuyên khoa I', '8 năm kinh nghiệm tai mũi họng', 'bs.dung@hospital.vn', '0912345004'),
(5, 'BS. Hoàng Văn Em', 5, 'Bác sĩ sản phụ khoa', '11 năm kinh nghiệm sản phụ khoa', 'bs.em@hospital.vn', '0912345005'),
(6, 'BS. Đặng Thị Phương', 6, 'Bác sĩ da liễu', '9 năm kinh nghiệm điều trị da liễu', 'bs.phuong@hospital.vn', '0912345006'),
(7, 'BS. Vũ Văn Giang', 7, 'Bác sĩ ung bướu', '14 năm kinh nghiệm điều trị ung thư', 'bs.giang@hospital.vn', '0912345007'),
(8, 'BS. Mai Thị Hoa', 8, 'Bác sĩ nội tổng quát', '10 năm kinh nghiệm nội khoa', 'bs.hoa@hospital.vn', '0912345008');

-- TUVAN (Consultation Requests)
CREATE TABLE `tuvan` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `HOTEN` varchar(100) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `SDT` varchar(20) NOT NULL,
  `MOTA` text NOT NULL,
  `IDKHOA` int(11) DEFAULT NULL,
  `IDBACSI` int(11) DEFAULT NULL,
  `ANH` varchar(255) DEFAULT NULL,
  `TRANGTHAI` varchar(50) DEFAULT 'Chờ xử lý',
  `PHANHOI` text DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  `NgayCapNhat` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID`),
  KEY `IDKHOA` (`IDKHOA`),
  KEY `IDBACSI` (`IDBACSI`),
  CONSTRAINT `tuvan_ibfk_1` FOREIGN KEY (`IDKHOA`) REFERENCES `khoa` (`IDKHOA`) ON DELETE SET NULL,
  CONSTRAINT `tuvan_ibfk_2` FOREIGN KEY (`IDBACSI`) REFERENCES `bacsi` (`ID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- LIENHE (Contact Messages)
CREATE TABLE `lienhe` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `HOTEN` varchar(100) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `NOIDUNGTN` text NOT NULL,
  `TRANGTHAI` varchar(50) DEFAULT 'Chưa xử lý',
  `PHANHOI` text DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  `NgayCapNhat` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- Staff auth table (for login)
-- =============================================

CREATE TABLE `nhanvien` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TAIKHOAN` varchar(100) NOT NULL,
  `MATKHAU` varchar(255) NOT NULL,
  `HOTEN` varchar(100) DEFAULT NULL,
  `EMAIL` varchar(100) DEFAULT NULL,
  `VAITRO` varchar(20) DEFAULT 'STAFF',
  `TRANGTHAI` varchar(20) DEFAULT 'ACTIVE',
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uniq_taikhoan` (`TAIKHOAN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- NOTE: Seed nhân viên via PHP (bcrypt): backend/scripts/seed_staff.php

-- =============================================
-- Original compatibility schema (optional)
-- =============================================

-- BacSi (compat)
CREATE TABLE `BacSi` (
  `MaBacSi` int(11) NOT NULL AUTO_INCREMENT,
  `HoTen` varchar(100) NOT NULL,
  `Khoa` varchar(100) DEFAULT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `TrangThai` varchar(20) DEFAULT 'DangHoatDong',
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MaBacSi`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- BenhNhan (compat)
CREATE TABLE `BenhNhan` (
  `MaBenhNhan` int(11) NOT NULL AUTO_INCREMENT,
  `HoTen` varchar(100) NOT NULL,
  `Tuoi` int(11) DEFAULT NULL,
  `GioiTinh` varchar(10) DEFAULT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MaBenhNhan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ChanDoanAI (compat)
CREATE TABLE `ChanDoanAI` (
  `MaChanDoan` int(11) NOT NULL AUTO_INCREMENT,
  `MaBenhNhan` int(11) NOT NULL,
  `TrieuChung` text DEFAULT NULL,
  `DuDoanBenh` varchar(255) DEFAULT NULL,
  `DoTinCay` float DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MaChanDoan`),
  KEY `fk_chandoanai_benhnhan` (`MaBenhNhan`),
  CONSTRAINT `fk_chandoanai_benhnhan` FOREIGN KEY (`MaBenhNhan`) REFERENCES `BenhNhan` (`MaBenhNhan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- DonThuoc (compat)
CREATE TABLE `DonThuoc` (
  `MaDonThuoc` int(11) NOT NULL AUTO_INCREMENT,
  `MaBenhNhan` int(11) NOT NULL,
  `MaBacSi` int(11) NOT NULL,
  `Thuoc` text DEFAULT NULL,
  `HuongDan` text DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MaDonThuoc`),
  KEY `fk_donthuoc_benhnhan` (`MaBenhNhan`),
  KEY `fk_donthuoc_bacsi` (`MaBacSi`),
  CONSTRAINT `fk_donthuoc_benhnhan` FOREIGN KEY (`MaBenhNhan`) REFERENCES `BenhNhan` (`MaBenhNhan`) ON DELETE CASCADE,
  CONSTRAINT `fk_donthuoc_bacsi` FOREIGN KEY (`MaBacSi`) REFERENCES `BacSi` (`MaBacSi`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- HoSoBenhNhan (compat)
CREATE TABLE `HoSoBenhNhan` (
  `MaHoSo` int(11) NOT NULL AUTO_INCREMENT,
  `MaBenhNhan` int(11) NOT NULL,
  `ChanDoan` text DEFAULT NULL,
  `HuongDieuTri` text DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MaHoSo`),
  KEY `fk_hoso_benhnhan` (`MaBenhNhan`),
  CONSTRAINT `fk_hoso_benhnhan` FOREIGN KEY (`MaBenhNhan`) REFERENCES `BenhNhan` (`MaBenhNhan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- LichHen (compat)
CREATE TABLE `LichHen` (
  `MaLichHen` int(11) NOT NULL AUTO_INCREMENT,
  `MaBenhNhan` int(11) NOT NULL,
  `MaBacSi` int(11) NOT NULL,
  `ThoiGianHen` datetime NOT NULL,
  `TrangThai` varchar(20) DEFAULT 'ChoXacNhan',
  `TrieuChung` text DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MaLichHen`),
  KEY `fk_lichhen_benhnhan` (`MaBenhNhan`),
  KEY `fk_lichhen_bacsi` (`MaBacSi`),
  CONSTRAINT `fk_lichhen_benhnhan` FOREIGN KEY (`MaBenhNhan`) REFERENCES `BenhNhan` (`MaBenhNhan`) ON DELETE CASCADE,
  CONSTRAINT `fk_lichhen_bacsi` FOREIGN KEY (`MaBacSi`) REFERENCES `BacSi` (`MaBacSi`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- Post-import summary (optional)
-- =============================================
SELECT 'Database import completed!' as Status;
SELECT COUNT(*) as 'Total Specialties' FROM khoa;
SELECT COUNT(*) as 'Total Doctors' FROM bacsi;
SELECT COUNT(*) as 'Total Consultations' FROM tuvan;
SELECT COUNT(*) as 'Total Contacts' FROM lienhe;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
