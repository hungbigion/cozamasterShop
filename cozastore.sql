-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2022 at 03:29 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cozastore`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `IDacc` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phonenum` text NOT NULL,
  `address` varchar(255) NOT NULL,
  `per` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`IDacc`, `username`, `password`, `fullname`, `email`, `phonenum`, `address`, `per`) VALUES
(1, 'admin', '123456', 'Nguyen Huu Hung', 'pthe99040@gmail.com', '0879332174', 'Hưng trung Hưng nguyên Nghệ An', 1),
(2, 'deptrai', '123456', 'Nguyen Huu Hung', 'hung@gmail.com', '0983340572', 'Vinh', 0),
(3, 'venus', '123456', 'hoang duc', 'venus@gmail.com', '09711985738', 'ninh binh									', 0),
(5, 'catre78', '123456', 'pham xuan an', 'ancatre@gmail.com', '0388023277', 'bệnh viện lào cai 																										', 0),
(8, 'test', '123456', 'cart success', 'toitest2@gmail.com', '0347831040', 'vinh', 2),
(9, 'nhatkhenh0912', '123456', 'Dinh Thi Nhat', 'nhatkhenh@gmail.com', '0348955766', 'Hưng Yên Hưng Nguyên Nghệ An 									', 0);

-- --------------------------------------------------------

--
-- Table structure for table `bill`
--

CREATE TABLE `bill` (
  `idbill` int(11) NOT NULL,
  `idcart` int(11) NOT NULL,
  `IDacc` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `phonenum` text NOT NULL,
  `address` varchar(255) NOT NULL,
  `dateout` date NOT NULL,
  `detailbill` text NOT NULL,
  `billtotal` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bill`
--

INSERT INTO `bill` (`idbill`, `idcart`, `IDacc`, `fullname`, `phonenum`, `address`, `dateout`, `detailbill`, `billtotal`) VALUES
(1, 4, 5, 'dau lung qua', '09090808', 'bệnh viện hahahahaha', '2022-05-25', ' Name: ANCCI AC13401 C2  QT: 1  TT: 760000 VND    Name: ANCCI AC13220 C1C  QT: 1  TT: 680000 VND    Name: BROMA 8040N TT  QT: 1  TT: 1225000 VND    ', 2665000),
(2, 4, 5, 'dau lung qua', '09090808', 'bệnh viện hahahahaha', '2022-05-25', ' Name:   ANCCI AC13141 C1C  QT: 2  TT: 1520000 VND    ', 1520000),
(3, 4, 5, 'dau lung qua', '09090808', 'bệnh viện hahahahaha', '2022-05-25', ' Name:   ANCCI AC13141 C1C  QT: 2  TT: 1520000 VND    ', 1520000),
(4, 4, 5, 'dau lung qua', '09090808', 'bệnh viện hahahahaha', '2022-05-25', ' Name:   ANCCI AC13141 C1C  QT: 2  TT: 1520000 VND    Name: ANCCI AC13401 C2  QT: 3  TT: 2280000 VND    ', 3800000),
(5, 4, 5, 'pham xuan an', '09090808', 'bệnh viện lào cai 																										', '2022-05-26', ' Name:   ANCCI AC13141 C1C  QT: 1  TT: 760000 VND    Name: BROMA 8040N TT  QT: 1  TT: 1225000 VND    ', 1985000),
(6, 4, 5, 'pham xuan an', '09090808', 'bệnh viện lào cai 																										', '2022-05-26', ' Name: OLIVE 66617 C1  QT: 1  TT: 1660000 VND    ', 1660000),
(7, 1, 2, 'Nguyen Huu Hung', '0879332174', 'Vinh', '2022-05-26', ' Name:   ANCCI AC13141 C1C  QT: 5  TT: 3800000 VND    Name: ANCCI AC13401 C2  QT: 1  TT: 760000 VND    Name: BROMA 8040N TT  QT: 1  TT: 1225000 VND    Name: ANCCI AC13220 C1C  QT: 1  TT: 680000 VND    ', 6465000),
(9, 1, 2, 'Nguyen Huu Hung', '0879332174', 'Vinh', '2022-05-27', ' Name: ANCCI AC13401 C2  QT: 4  TT: 3040000 VND    ', 3040000),
(10, 8, 9, 'Dinh Thi Nhat', '0987332174', 'Hưng Yên Hưng Nguyên Nghệ An ', '2022-05-27', ' Name: ANCCI AC13220 C1C  QT: 3  TT: 2040000 VND    Name: BROMA 8040N TT  QT: 1  TT: 1225000 VND    ', 3265000),
(11, 4, 5, 'pham xuan an', '0388023277', 'bệnh viện lào cai 																										', '2022-05-29', ' Name: ANCCI AC13401 C2  QT: 2  TT: 1520000 VND    ', 1520000);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `idcart` int(11) NOT NULL,
  `IDacc` int(11) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`idcart`, `IDacc`, `total`) VALUES
(1, 2, 9220000),
(2, 3, 7970000),
(3, 4, 0),
(4, 5, 0),
(6, 8, 0),
(7, 1, 0),
(8, 9, 1520000);

-- --------------------------------------------------------

--
-- Table structure for table `cartdetail`
--

CREATE TABLE `cartdetail` (
  `idcartdetail` int(11) NOT NULL,
  `idcart` int(11) NOT NULL,
  `IDacc` int(11) NOT NULL,
  `idproduct` int(11) NOT NULL,
  `namepd` varchar(255) NOT NULL,
  `quantitycart` int(255) NOT NULL,
  `price` int(255) NOT NULL,
  `totalpd` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cartdetail`
--

INSERT INTO `cartdetail` (`idcartdetail`, `idcart`, `IDacc`, `idproduct`, `namepd`, `quantitycart`, `price`, `totalpd`) VALUES
(15, 2, 3, 11, 'ANCCI AC13401 C2', 2, 760000, 1520000),
(16, 2, 3, 13, 'BROMA 8040N TT', 2, 1225000, 2450000),
(18, 2, 3, 15, 'OLIVE 66617 C1', 2, 1660000, 3320000),
(19, 2, 3, 12, 'ANCCI AC13220 C1C', 1, 680000, 680000),
(41, 1, 2, 11, 'ANCCI AC13401 C2', 3, 760000, 2280000),
(42, 1, 2, 13, 'BROMA 8040N TT', 4, 1225000, 4900000),
(43, 1, 2, 12, 'ANCCI AC13220 C1C', 3, 680000, 2040000),
(47, 8, 9, 11, 'ANCCI AC13401 C2', 2, 760000, 1520000);

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `idcontact` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`idcontact`, `email`, `date`, `message`) VALUES
(1, 'daulung@gmail.com', '2022-05-24', 'I want to buy frames in bulk'),
(2, 'daulung@gmail.com', '2022-05-23', 'Hello my friend'),
(5, 'today@gmail.com', '2022-05-26', 'test thui '),
(6, 'nhatkhenh0912@gmail.com', '2022-05-27', 'Yêu anh hùng '),
(7, 'nhatkhenh@gmail.com', '2022-05-27', 'nhớ anh hùng ');

-- --------------------------------------------------------

--
-- Table structure for table `material`
--

CREATE TABLE `material` (
  `idmaterial` int(11) NOT NULL,
  `materialname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `material`
--

INSERT INTO `material` (`idmaterial`, `materialname`) VALUES
(1, 'Nhựa'),
(2, 'Nhôm'),
(3, 'Titan'),
(6, 'Thép không gỉ');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `idproduct` int(11) NOT NULL,
  `namepd` varchar(255) NOT NULL,
  `idmaterial` int(11) NOT NULL,
  `idstyle` int(11) NOT NULL,
  `price` int(255) NOT NULL,
  `pdquantity` int(255) NOT NULL,
  `detail` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `sttus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`idproduct`, `namepd`, `idmaterial`, `idstyle`, `price`, `pdquantity`, `detail`, `image`, `sttus`) VALUES
(5, 'ANCCI AC13141 C5C', 3, 1, 760000, 12, '  Gọng kính Ancci AC13141 C5C với thiết kế dáng vuông đa giác cá tính, hợp thời trang với mọi lứa tuổi, đem lại cảm giác mới mẻ và năng động cho người sử dụng.  ', 'uploaded_img/AC13141_C5C.jpg', 3),
(9, '  ANCCI AC13141 C1C', 3, 1, 760000, 0, 'Gọng kính Ancci AC13141 C1C với thiết kế dáng vuông-đa giác cá tính, hợp thời trang với mọi lứa tuổi, đem lại cảm giác mới mẻ và năng động cho người sử dụng.', 'uploaded_img/ANCCI_AC13141_C1C.jpg', 1),
(11, 'ANCCI AC13401 C2', 1, 1, 760000, 4, 'Gọng kính Ancci AC13401 C2 với thiết kế dáng mắt vuông, hợp thời trang với mọi lứa tuổi, đem lại cảm giác mới mẻ và năng động cho người sử dụng.', 'uploaded_img/ANCCI_AC13401_C2.jpg', 1),
(12, 'ANCCI AC13220 C1C', 3, 1, 680000, 5, 'Gọng kính Ancci AC13220 C1C  với thiết kế dáng vuông trẻ trung, hợp thời trang với mọi lứa tuổi, đem lại cảm giác mới mẻ và năng động cho người sử dụng.', 'uploaded_img/ANCC_AC13220_C1C.jpg', 2),
(13, 'BROMA 8040N TT', 1, 1, 1225000, 11, 'Gọng kính Broma 8040N TT là sản phẩm thuộc bộ sưu tập gọng kính nhựa acetate đang được ưa chuộng hiện nay. Kích thước trung bình rất dễ đeo cùng với dáng mắt chữ nhật hợp thời trang, mẫu kính này sẽ giúp cho vẻ ngoài của bạn thêm phần trẻ trung và phong cách hơn.', 'uploaded_img/8040N-TT.jpg', 1),
(15, 'OLIVE 66617 C1', 3, 4, 1660000, 7, 'Gọng kính OLIVE 66617 C1 là sản phẩm thuộc bộ sưu tập gọng kính titan đang được ưa chuộng hiện nay. Gọng kính làm bằng chất liệu nhựa kết hợp kim titan, nhẹ, bền, càng kính thiết kế mảnh và thanh thoát.', 'uploaded_img/GTTCT-Olive-66617-C1.jpg', 2),
(16, 'BOLON BJ7081 B10 C', 2, 2, 2980000, 12, 'Bolon là một thương hiệu đến từ Ý và sử dụng chất liệu cao cấp đến từ Thuỵ Sĩ. Thiết kế của gọng kính BOLON BJ7081 B10 C luôn đảm bảo được sự sắc nét, thiết kế tinh tế và bền bỉ trong quá trình sử dụng.', 'uploaded_img/BOLON-BJ7081-B10.jpg', 1),
(18, 'CHARRIOL PC 749 1C0454', 3, 4, 10500000, 21, '  Với kiểu dáng cổ điển pha lẫn với hiện đại, truyền thống xen lẫn với độc đáo, đường nét tinh xảo, chăm chút tỉ mỉ từ những chi tiết nhỏ nhất, Gọng kính Charriol PC 749 1C0454 góp phần nâng tầm đẳng cấp cho bạn.  ', 'uploaded_img/Charriol-PC-749-1C0454.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `style`
--

CREATE TABLE `style` (
  `idstyle` int(11) NOT NULL,
  `namestyle` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `style`
--

INSERT INTO `style` (`idstyle`, `namestyle`) VALUES
(1, 'Vành liền'),
(2, 'Nửa gọng'),
(4, 'Không gọng');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`IDacc`);

--
-- Indexes for table `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`idbill`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`idcart`);

--
-- Indexes for table `cartdetail`
--
ALTER TABLE `cartdetail`
  ADD PRIMARY KEY (`idcartdetail`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`idcontact`);

--
-- Indexes for table `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`idmaterial`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`idproduct`);

--
-- Indexes for table `style`
--
ALTER TABLE `style`
  ADD PRIMARY KEY (`idstyle`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `IDacc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `bill`
--
ALTER TABLE `bill`
  MODIFY `idbill` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `idcart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cartdetail`
--
ALTER TABLE `cartdetail`
  MODIFY `idcartdetail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `idcontact` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `material`
--
ALTER TABLE `material`
  MODIFY `idmaterial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `idproduct` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `style`
--
ALTER TABLE `style`
  MODIFY `idstyle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
