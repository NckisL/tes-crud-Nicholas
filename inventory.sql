-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2026 at 12:43 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `cat_id` varchar(15) NOT NULL,
  `cat_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`cat_id`, `cat_name`) VALUES
('CAT-261109002', 'ATK'),
('CAT-261109001', 'Elektronik'),
('CAT-261109005', 'Kebersihan'),
('CAT-261109004', 'Kemasan'),
('CAT-261109003', 'Peralatan');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_id` varchar(15) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_cat_id` varchar(15) NOT NULL,
  `item_stock` int(11) NOT NULL DEFAULT '0',
  `item_price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`item_id`, `item_name`, `item_cat_id`, `item_stock`, `item_price`) VALUES
('ITM-261109001', 'Kabel HDMI 2m', 'CAT-261109001', 50, '35000.00'),
('ITM-261109002', 'Kertas A4 80gsm', 'CAT-261109002', 100, '55000.00'),
('ITM-261109003', 'Obeng Set', 'CAT-261109003', 20, '75000.00'),
('ITM-261109004', 'Lakban Bening', 'CAT-261109004', 80, '12000.00'),
('ITM-261109005', 'Mouse Wireless', 'CAT-261109001', 30, '95000.00'),
('ITM-261109006', 'Pulpen Hitam', 'CAT-261109002', 200, '3500.00'),
('ITM-261109007', 'Kardus Sedang', 'CAT-261109004', 60, '8000.00'),
('ITM-261109008', 'Sapu Lantai', 'CAT-261109005', 15, '25000.00');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `trx_id` varchar(15) NOT NULL,
  `trx_item_id` varchar(15) NOT NULL,
  `trx_status` enum('Y','N') NOT NULL COMMENT 'Y = masuk, N = keluar',
  `trx_amount` int(11) NOT NULL,
  `trx_date` date NOT NULL,
  `trx_desc` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`trx_id`, `trx_item_id`, `trx_status`, `trx_amount`, `trx_date`, `trx_desc`) VALUES
('TRX-261109001', 'ITM-261109001', 'Y', 20, '2026-11-05', 'Restock dari supplier'),
('TRX-261109002', 'ITM-261109002', 'N', 10, '2026-11-06', 'Dipakai kantor'),
('TRX-261109003', 'ITM-261109003', 'Y', 5, '2026-11-07', 'Pembelian baru'),
('TRX-261109004', 'ITM-261109004', 'N', 15, '2026-11-08', 'Dikirim ke cabang'),
('TRX-261109005', 'ITM-261109005', 'Y', 10, '2026-11-09', 'Restock'),
('TRX-261109006', 'ITM-261109006', 'N', 25, '2026-11-09', 'Kebutuhan harian');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `cat_name` (`cat_name`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `item_cat_id` (`item_cat_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`trx_id`),
  ADD KEY `trx_item_id` (`trx_item_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`item_cat_id`) REFERENCES `category` (`cat_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`trx_item_id`) REFERENCES `item` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
