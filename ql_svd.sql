-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: ql_svd
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `baocao`
--

DROP TABLE IF EXISTS `baocao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `baocao` (
  `MaBaoCao` int NOT NULL AUTO_INCREMENT,
  `NgayBaoCao` date NOT NULL,
  `TongTienSan` decimal(18,2) DEFAULT NULL,
  `TongTienDichVu` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`MaBaoCao`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `baocao`
--

LOCK TABLES `baocao` WRITE;
/*!40000 ALTER TABLE `baocao` DISABLE KEYS */;
INSERT INTO `baocao` VALUES (3,'2025-12-31',960000.00,30000.00),(12,'2025-12-13',400000.00,15000.00),(13,'2025-12-12',116000.00,0.00),(14,'2025-12-12',84000.00,0.00),(15,'2025-12-12',84000.00,0.00),(16,'2025-12-12',213400.00,0.00),(17,'2025-12-12',202400.00,0.00),(18,'2025-12-12',54400.00,0.00),(19,'2025-12-19',20000.00,30000.00),(20,'2025-12-19',16000.00,0.00),(21,'2025-12-19',15400.00,0.00),(22,'2025-12-22',1100000.00,30000.00),(23,'2025-12-23',660000.00,0.00),(24,'2026-01-15',200000.00,0.00),(25,'2026-01-14',400000.00,0.00),(26,'2026-01-15',16000.00,0.00),(27,'2026-01-16',200000.00,0.00);
/*!40000 ALTER TABLE `baocao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bienthesanpham`
--

DROP TABLE IF EXISTS `bienthesanpham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bienthesanpham` (
  `MaBienThe` int NOT NULL AUTO_INCREMENT,
  `MaSanPham` int NOT NULL,
  `KichThuoc` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SoLuongTon` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`MaBienThe`),
  KEY `FK_BienThe_SanPham` (`MaSanPham`),
  CONSTRAINT `FK_BienThe_SanPham` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`MaSanPham`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bienthesanpham`
--

LOCK TABLES `bienthesanpham` WRITE;
/*!40000 ALTER TABLE `bienthesanpham` DISABLE KEYS */;
INSERT INTO `bienthesanpham` VALUES (1,1,'40',10),(2,1,'41',15),(3,1,'42',12),(4,1,'43',5),(5,2,'M',20),(6,2,'L',25),(7,2,'XL',14),(8,3,'Standard',80),(9,4,'41',8),(10,4,'42',10),(11,4,'43',12),(12,4,'44',7),(13,5,'M',30),(14,5,'L',22),(15,5,'XL',15);
/*!40000 ALTER TABLE `bienthesanpham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chitietdichvu`
--

DROP TABLE IF EXISTS `chitietdichvu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chitietdichvu` (
  `MaDatSan` int NOT NULL,
  `MaDV` int NOT NULL,
  `SoLuong` int NOT NULL,
  `DonGia` decimal(12,2) NOT NULL,
  `ThanhTien` decimal(23,2) GENERATED ALWAYS AS ((`SoLuong` * `DonGia`)) STORED,
  PRIMARY KEY (`MaDatSan`,`MaDV`),
  KEY `FK_ChiTietDichVu` (`MaDV`),
  CONSTRAINT `FK_ChiTietDichVu` FOREIGN KEY (`MaDV`) REFERENCES `dichvu` (`MaDV`),
  CONSTRAINT `FK_ChiTietDichVu_MaDatSan` FOREIGN KEY (`MaDatSan`) REFERENCES `datsan` (`MaDatSan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chitietdichvu`
--

LOCK TABLES `chitietdichvu` WRITE;
/*!40000 ALTER TABLE `chitietdichvu` DISABLE KEYS */;
INSERT INTO `chitietdichvu` (`MaDatSan`, `MaDV`, `SoLuong`, `DonGia`) VALUES (7,1,2,15000.00),(16,1,1,15000.00),(23,1,2,15000.00),(26,1,2,15000.00);
/*!40000 ALTER TABLE `chitietdichvu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chitietdonhang`
--

DROP TABLE IF EXISTS `chitietdonhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chitietdonhang` (
  `MaDonHang` int NOT NULL,
  `MaSanPham` int NOT NULL,
  `MaBienThe` int NOT NULL,
  `SoLuong` int NOT NULL,
  `DonGia` decimal(10,2) NOT NULL,
  PRIMARY KEY (`MaDonHang`,`MaBienThe`),
  KEY `FK_CTDH_SanPham` (`MaSanPham`),
  KEY `FK_CTDH_BienThe` (`MaBienThe`),
  CONSTRAINT `FK_CTDH_BienThe` FOREIGN KEY (`MaBienThe`) REFERENCES `bienthesanpham` (`MaBienThe`),
  CONSTRAINT `FK_CTDH_DonHang` FOREIGN KEY (`MaDonHang`) REFERENCES `donhang` (`MaDonHang`) ON DELETE CASCADE,
  CONSTRAINT `FK_CTDH_SanPham` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`MaSanPham`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chitietdonhang`
--

LOCK TABLES `chitietdonhang` WRITE;
/*!40000 ALTER TABLE `chitietdonhang` DISABLE KEYS */;
INSERT INTO `chitietdonhang` VALUES (1,5,14,3,850000.00),(2,2,7,1,750000.00);
/*!40000 ALTER TABLE `chitietdonhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `danhgia`
--

DROP TABLE IF EXISTS `danhgia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `danhgia` (
  `MaDG` int NOT NULL AUTO_INCREMENT,
  `MaKH` int NOT NULL,
  `MaSan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Diem` tinyint unsigned NOT NULL,
  `NoiDung` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayDanhGia` datetime DEFAULT NULL,
  `MaDatSan` int DEFAULT NULL,
  PRIMARY KEY (`MaDG`),
  KEY `FK_DanhGia_MaKH` (`MaKH`),
  KEY `FK_DanhGia_MaSan` (`MaSan`),
  KEY `FK_DanhGia_MaDatSan` (`MaDatSan`),
  CONSTRAINT `FK_DanhGia_MaDatSan` FOREIGN KEY (`MaDatSan`) REFERENCES `datsan` (`MaDatSan`),
  CONSTRAINT `FK_DanhGia_MaKH` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`),
  CONSTRAINT `FK_DanhGia_MaSan` FOREIGN KEY (`MaSan`) REFERENCES `san` (`MaSan`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `danhgia`
--

LOCK TABLES `danhgia` WRITE;
/*!40000 ALTER TABLE `danhgia` DISABLE KEYS */;
INSERT INTO `danhgia` VALUES (1,1,'SB6',4,'Sân đẹp','2025-12-12 23:11:07',22);
/*!40000 ALTER TABLE `danhgia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datsan`
--

DROP TABLE IF EXISTS `datsan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `datsan` (
  `MaDatSan` int NOT NULL AUTO_INCREMENT,
  `MaKH` int NOT NULL,
  `MaSan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `NgayDat` date NOT NULL,
  `GioBatDau` time(6) NOT NULL,
  `GioKetThuc` time(6) NOT NULL,
  `TongTien` decimal(12,2) DEFAULT NULL,
  `TrangThai` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GhiChu` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`MaDatSan`),
  KEY `FK_DatSan_MaKH` (`MaKH`),
  KEY `FK_DatSan_MaSan` (`MaSan`),
  CONSTRAINT `FK_DatSan_MaKH` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`),
  CONSTRAINT `FK_DatSan_MaSan` FOREIGN KEY (`MaSan`) REFERENCES `san` (`MaSan`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datsan`
--

LOCK TABLES `datsan` WRITE;
/*!40000 ALTER TABLE `datsan` DISABLE KEYS */;
INSERT INTO `datsan` VALUES (7,4,'SB6','2025-12-31','15:00:00.000000','18:00:00.000000',960000.00,'Đã đặt sân',''),(16,1,'SB2','2025-12-13','14:07:00.000000','16:07:00.000000',400000.00,'Đã đặt sân',''),(17,1,'SB1','2025-12-12','22:05:00.000000','22:40:00.000000',116000.00,'Đã đặt sân',''),(18,1,'SB2','2025-12-12','22:05:00.000000','22:30:00.000000',84000.00,'Đã đặt sân',''),(19,1,'SB2','2025-12-12','22:35:00.000000','23:00:00.000000',84000.00,'Đã đặt sân',''),(20,1,'SB3','2025-12-12','22:04:00.000000','23:02:00.000000',213400.00,'Đã đặt sân',''),(21,1,'SB4','2025-12-12','22:10:00.000000','23:05:00.000000',202400.00,'Đã đặt sân',''),(22,1,'SB6','2025-12-12','22:05:00.000000','22:15:00.000000',54400.00,'Đã đặt sân',''),(23,2,'SB1','2025-12-19','22:09:00.000000','22:15:00.000000',20000.00,'Đã đặt sân',''),(24,2,'SB2','2025-12-19','22:10:00.000000','22:15:00.000000',16000.00,'Đã đặt sân',''),(25,2,'SB3','2025-12-19','22:10:00.000000','22:14:00.000000',15400.00,'Đã đặt sân',''),(26,1,'SB3','2025-12-22','13:30:00.000000','18:30:00.000000',1100000.00,'Đã hủy',''),(27,1,'SB4','2025-12-23','15:35:00.000000','18:35:00.000000',660000.00,'Đã hủy',''),(28,1,'SB1','2026-01-15','16:26:00.000000','17:26:00.000000',200000.00,'Đã hủy',''),(29,1,'SB1','2026-01-14','11:50:00.000000','13:50:00.000000',400000.00,'Đã đặt sân',''),(30,1,'SB1','2026-01-15','08:55:00.000000','09:00:00.000000',16000.00,'Đã đặt sân',''),(31,1,'SB1','2026-01-16','11:00:00.000000','12:00:00.000000',200000.00,'Đã đặt sân','');
/*!40000 ALTER TABLE `datsan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dichvu`
--

DROP TABLE IF EXISTS `dichvu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dichvu` (
  `MaDV` int NOT NULL AUTO_INCREMENT,
  `TenDV` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `DonGia` decimal(12,2) NOT NULL,
  `MoTa` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SoLuongTon` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`MaDV`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dichvu`
--

LOCK TABLES `dichvu` WRITE;
/*!40000 ALTER TABLE `dichvu` DISABLE KEYS */;
INSERT INTO `dichvu` VALUES (1,'Aquafina',15000.00,'Chai nước Aquafina 0,5L',16),(2,'Sting',20000.00,'Nước uống năng lượng Stinger 330ml',5),(3,'Pocari',18000.00,'Nước điện giải Pocari Sweat 500ml',0),(4,'Thuê giày',50000.00,'Cho thuê giày thi đấu cho 1 người',0),(5,'Thuê vớ',10000.00,'Cho thuê vớ thi đấu 1 đôi',0),(6,'Thuê quần áo',70000.00,'Thuê bộ quần áo thi đấu cho cả đội',0),(8,'TestDV2',30000.00,'Test Dịch VỤ 2',2);
/*!40000 ALTER TABLE `dichvu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donhang`
--

DROP TABLE IF EXISTS `donhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donhang` (
  `MaDonHang` int NOT NULL AUTO_INCREMENT,
  `MaKH` int NOT NULL,
  `TenKH` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SDT` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DiaChi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TongTien` decimal(10,2) NOT NULL,
  `PhuongThucThanhToan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TrangThai` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Chờ xử lý',
  `NgayTao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`MaDonHang`),
  KEY `FK_DonHang_KhachHang` (`MaKH`),
  CONSTRAINT `FK_DonHang_KhachHang` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donhang`
--

LOCK TABLES `donhang` WRITE;
/*!40000 ALTER TABLE `donhang` DISABLE KEYS */;
INSERT INTO `donhang` VALUES (1,1,'Hoàng Anh Trần Gia','22222222','hoanganhfanmu@gmail.com','HangMU',2550000.00,'Chuyển khoản ngân hàng','Chờ xử lý','2025-12-08 11:57:27'),(2,1,'Hoàng Anh Trần Gia','22222222','hoanganhfanmu@gmail.com','HangMU',750000.00,'Thanh toán khi nhận hàng','Chờ xử lý','2025-12-19 16:00:09');
/*!40000 ALTER TABLE `donhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hoadon`
--

DROP TABLE IF EXISTS `hoadon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hoadon` (
  `MaHD` int NOT NULL AUTO_INCREMENT,
  `MaDatSan` int NOT NULL,
  `NgayLap` datetime DEFAULT NULL,
  `TongTien` decimal(14,2) NOT NULL,
  `GhiChu` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MaBaoCao` int DEFAULT NULL,
  `TenPhuongThuc` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Thanh toán tại sân',
  PRIMARY KEY (`MaHD`),
  KEY `FK_HoaDon_MaDatSan` (`MaDatSan`),
  KEY `FK_HoaDon_MaBaoCao` (`MaBaoCao`),
  CONSTRAINT `FK_HoaDon_MaBaoCao` FOREIGN KEY (`MaBaoCao`) REFERENCES `baocao` (`MaBaoCao`),
  CONSTRAINT `FK_HoaDon_MaDatSan` FOREIGN KEY (`MaDatSan`) REFERENCES `datsan` (`MaDatSan`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hoadon`
--

LOCK TABLES `hoadon` WRITE;
/*!40000 ALTER TABLE `hoadon` DISABLE KEYS */;
INSERT INTO `hoadon` VALUES (3,7,'2025-12-31 00:00:00',990000.00,'',3,'Thanh toán tại sân'),(12,16,'2025-12-13 00:00:00',415000.00,'',12,'Thanh toán tại sân'),(13,17,'2025-12-12 00:00:00',116000.00,'',13,'Thanh toán tại sân'),(14,18,'2025-12-12 00:00:00',84000.00,'',14,'Chuyển khoản'),(15,19,'2025-12-12 00:00:00',84000.00,'',15,'Thanh toán tại sân'),(16,20,'2025-12-12 00:00:00',213400.00,'',16,'Chuyển khoản'),(17,21,'2025-12-12 00:00:00',202400.00,'',17,'Chuyển khoản'),(18,22,'2025-12-12 00:00:00',54400.00,'',18,'Chuyển khoản'),(19,23,'2025-12-19 00:00:00',50000.00,'',19,'Thanh toán tại sân'),(20,24,'2025-12-19 00:00:00',16000.00,'',20,'Thanh toán tại sân'),(21,25,'2025-12-19 00:00:00',15400.00,'',21,'Chuyển khoản'),(22,26,'2025-12-22 00:00:00',1130000.00,'',22,'Thanh toán tại sân'),(23,27,'2025-12-23 00:00:00',660000.00,'',23,'Chuyển khoản'),(24,28,'2026-01-15 00:00:00',200000.00,'',24,'Thanh toán tại sân'),(25,29,'2026-01-14 00:00:00',400000.00,'',25,'Thanh toán tại sân'),(26,30,'2026-01-15 00:00:00',16000.00,'',26,'Thanh toán tại sân'),(27,31,'2026-01-16 00:00:00',200000.00,'',27,'Thanh toán tại sân');
/*!40000 ALTER TABLE `hoadon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `khachhang`
--

DROP TABLE IF EXISTS `khachhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `khachhang` (
  `MaKH` int NOT NULL AUTO_INCREMENT,
  `HoTen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `SDT` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DiaChi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MaTK` int NOT NULL,
  PRIMARY KEY (`MaKH`),
  KEY `FK_KhachHang_MaTK` (`MaTK`),
  CONSTRAINT `FK_KhachHang_MaTK` FOREIGN KEY (`MaTK`) REFERENCES `taikhoan` (`MaTK`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `khachhang`
--

LOCK TABLES `khachhang` WRITE;
/*!40000 ALTER TABLE `khachhang` DISABLE KEYS */;
INSERT INTO `khachhang` VALUES (1,'Hoàng Anh Trần Gia','22222222','hoanganhfanmu@gmail.com','HangMU',2),(2,'Phúc Vinh','0877399514','phucvinh5924@gmail.com','q8',3),(3,'Trần Gia Hoàng Anh','0123456789','trangiahoanganh1@gmail.com','Tân Phú HCM',4),(4,'Văn Vinh','0123456789','vinhvdp24@gmail.com','LA',5),(5,'Văn Vinh','0877399514','vinhvdp24@gmail.com','LA',6);
/*!40000 ALTER TABLE `khachhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loaisan`
--

DROP TABLE IF EXISTS `loaisan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loaisan` (
  `MaLoai` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `TenLoai` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `MoTa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`MaLoai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loaisan`
--

LOCK TABLES `loaisan` WRITE;
/*!40000 ALTER TABLE `loaisan` DISABLE KEYS */;
INSERT INTO `loaisan` VALUES ('bd','Bóng đá','Sân bóng đá'),('cl','Cầu lông','Sân cầu lông'),('pkb','Pickleball','Sân pickleball');
/*!40000 ALTER TABLE `loaisan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nhaphang`
--

DROP TABLE IF EXISTS `nhaphang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nhaphang` (
  `MaNhap` int NOT NULL AUTO_INCREMENT,
  `MaDV` int NOT NULL,
  `SoLuongNhap` int NOT NULL,
  `NgayNhap` datetime NOT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`MaNhap`),
  KEY `FK_NhapHang_DichVu` (`MaDV`),
  CONSTRAINT `FK_NhapHang_DichVu` FOREIGN KEY (`MaDV`) REFERENCES `dichvu` (`MaDV`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nhaphang`
--

LOCK TABLES `nhaphang` WRITE;
/*!40000 ALTER TABLE `nhaphang` DISABLE KEYS */;
INSERT INTO `nhaphang` VALUES (1,1,20,'2025-11-27 23:06:53','Nhập aqua'),(2,1,20,'2025-12-12 14:09:49',''),(3,2,5,'2025-12-12 14:17:05',''),(4,8,2,'2025-12-19 22:07:57','a');
/*!40000 ALTER TABLE `nhaphang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nhapsanpham`
--

DROP TABLE IF EXISTS `nhapsanpham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nhapsanpham` (
  `MaNhap` int NOT NULL AUTO_INCREMENT,
  `MaBienThe` int NOT NULL,
  `SoLuongNhap` int NOT NULL,
  `NhaCungCap` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayNhap` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `GhiChu` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`MaNhap`),
  KEY `FK_NhapSP_BienThe` (`MaBienThe`),
  CONSTRAINT `FK_NhapSP_BienThe_New` FOREIGN KEY (`MaBienThe`) REFERENCES `bienthesanpham` (`MaBienThe`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nhapsanpham`
--

LOCK TABLES `nhapsanpham` WRITE;
/*!40000 ALTER TABLE `nhapsanpham` DISABLE KEYS */;
/*!40000 ALTER TABLE `nhapsanpham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `passwordreset`
--

DROP TABLE IF EXISTS `passwordreset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `passwordreset` (
  `id` int NOT NULL AUTO_INCREMENT,
  `MaKH` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `token_idx` (`token`),
  KEY `MaKH` (`MaKH`),
  CONSTRAINT `passwordreset_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `passwordreset`
--

LOCK TABLES `passwordreset` WRITE;
/*!40000 ALTER TABLE `passwordreset` DISABLE KEYS */;
INSERT INTO `passwordreset` VALUES (6,2,'ce97c6c3eb724ae160a0dff3774264ad5590380c12eb9d982b7d74e498337d63','2026-04-21 17:05:59');
/*!40000 ALTER TABLE `passwordreset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `san`
--

DROP TABLE IF EXISTS `san`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `san` (
  `MaSan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `TenSan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `GiaThue` decimal(12,2) NOT NULL,
  `TinhTrang` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MoTa` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MaLoai` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`MaSan`),
  KEY `FK_San_LoaiSan` (`MaLoai`),
  CONSTRAINT `FK_San_LoaiSan` FOREIGN KEY (`MaLoai`) REFERENCES `loaisan` (`MaLoai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `san`
--

LOCK TABLES `san` WRITE;
/*!40000 ALTER TABLE `san` DISABLE KEYS */;
INSERT INTO `san` VALUES ('SB1','Sân bóng A1',200000.00,'Hoạt động','Sân cỏ nhân tạo, có đèn chiếu sáng','bd'),('SB2','Sân bóng A2',200000.00,'Hoạt động','Sân gần khu vực để xe, thoáng mát','bd'),('SB3','Sân bóng B1',220000.00,'Hoạt động','Sân tiêu chuẩn mini, có mái che một phần','bd'),('SB4','Sân bóng B2',220000.00,'Bảo trì','Đang được nâng cấp mặt cỏ','bd'),('SB5','Sân bóng C1',300000.00,'Hoạt động','Sân lớn, cỏ nhân tạo chất lượng cao','bd'),('SB6','Sân bóng C2',320000.00,'Hoạt động','Sân tiêu chuẩn thi đấu phong trào','bd'),('SCL1','Sân cầu lông 1',80000.00,'Hoạt động','Sân trong nhà, mặt thảm chuẩn','cl'),('SCL2','Sân cầu lông 2',80000.00,'Hoạt động','Có đèn chiếu sáng LED','cl'),('SPKB1','Sân Pickleball 1',120000.00,'Hoạt động','Sân ngoài trời tiêu chuẩn','pkb');
/*!40000 ALTER TABLE `san` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sanpham`
--

DROP TABLE IF EXISTS `sanpham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sanpham` (
  `MaSanPham` int NOT NULL AUTO_INCREMENT,
  `TenSanPham` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MoTa` text COLLATE utf8mb4_unicode_ci,
  `DonGia` decimal(10,2) NOT NULL,
  `DanhMuc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HinhAnh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`MaSanPham`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sanpham`
--

LOCK TABLES `sanpham` WRITE;
/*!40000 ALTER TABLE `sanpham` DISABLE KEYS */;
INSERT INTO `sanpham` VALUES (1,'Giày Phantom Luna','Giày bóng đá chuyên nghiệp, phù hợp cho sân cỏ nhân tạo.',1200000.00,'Giày','public/images/products/phantomLuna.jpg','2025-12-08 11:15:12'),(2,'Áo Đấu CLB Hà Nội','Áo đấu chính thức mùa giải 2025 của CLB Hà Nội.',750000.00,'Áo','public/images/products/AoCLBHaNoi.jpg','2025-12-08 11:15:12'),(3,'Bóng thi đấu Official','Bóng thi đấu tiêu chuẩn quốc tế, độ nảy tốt.',450000.00,'Phụ kiện','public/images/products/Bóng thi đấu Official.jpg','2025-12-08 11:15:12'),(4,'Giày Predator Freak','Kiểm soát bóng toàn diện với thiết kế độc đáo.',2500000.00,'Giày','public/images/products/Giày Predator Freak.jpg','2025-12-08 11:15:12'),(5,'Áo Đấu Man City','Áo đấu sân nhà của Manchester City mùa giải mới.',850000.00,'Áo','public/images/products/AoMU.jpg','2025-12-08 11:15:12');
/*!40000 ALTER TABLE `sanpham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taikhoan`
--

DROP TABLE IF EXISTS `taikhoan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taikhoan` (
  `MaTK` int NOT NULL AUTO_INCREMENT,
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `LoaiTK` varchar(20) NOT NULL,
  PRIMARY KEY (`MaTK`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taikhoan`
--

LOCK TABLES `taikhoan` WRITE;
/*!40000 ALTER TABLE `taikhoan` DISABLE KEYS */;
INSERT INTO `taikhoan` VALUES (1,'vinh','$2y$10$N2k9kyFfx7LB/i7Xoik/0O80b9ObSTBegZSCnYOLQX6bGOIEf4NLG','admin'),(2,'anh','$2y$10$GDBZtjYSLJ8NrC1ONmBT8.L78nVaPRW/JXrnB5APfZmwguRTLeG.m','user'),(3,'phucvinh','$2y$10$KeaRVAX/utCyzEYX99MKPuI1CRySTma1p2uvM5rtctePQ05F0x3E2','user'),(4,'hoanganh','$2y$10$lW39/.5uGJzatjAB1IWqz.2rF5i.4iKvuS0/TuUBZXY/lcxzJRq0K','user'),(5,'vinhvan','$2y$10$7aDY7d94JTL8rjhcErm1DeCy1/ATOOvkppULikKu4qr5mkaN1PGQu','user'),(6,'vanvinh','$2y$10$OjALfFIjNpPIuCeXIk4GQup.iNz2jGGPs33AzMbdREf66NGFYX92K','user');
/*!40000 ALTER TABLE `taikhoan` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-28 14:03:36
