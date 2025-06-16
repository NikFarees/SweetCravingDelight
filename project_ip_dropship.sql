-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2025 at 10:02 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_ip_dropship`
--

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `agent_id` mediumint(8) UNSIGNED NOT NULL,
  `agentName` varchar(50) NOT NULL,
  `agentEmail` varchar(40) NOT NULL,
  `agentPassword` char(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`agent_id`, `agentName`, `agentEmail`, `agentPassword`) VALUES
(1, 'nikfarees', 'nikfarees@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef'),
(2, 'aiman123', 'aiman123@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef'),
(3, 'jack', 'jack@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef');

-- --------------------------------------------------------

--
-- Table structure for table `agents_approval`
--

CREATE TABLE `agents_approval` (
  `agent_approval_id` mediumint(8) UNSIGNED NOT NULL,
  `agent_id` mediumint(8) UNSIGNED NOT NULL,
  `supplier_id` mediumint(8) UNSIGNED NOT NULL,
  `applyDate` date NOT NULL,
  `approval_status` varchar(20) DEFAULT 'pending',
  `approval_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agents_approval`
--

INSERT INTO `agents_approval` (`agent_approval_id`, `agent_id`, `supplier_id`, `applyDate`, `approval_status`, `approval_date`) VALUES
(1, 1, 1, '2024-09-15', 'approved', '2024-09-15'),
(2, 2, 1, '2024-11-14', 'approved', '2024-11-14'),
(3, 3, 1, '2025-06-15', 'approved', '2025-06-15'),
(4, 3, 2, '2025-06-17', 'approved', '2025-06-17');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` mediumint(8) UNSIGNED NOT NULL,
  `agent_id` mediumint(8) UNSIGNED NOT NULL,
  `supplier_id` mediumint(8) UNSIGNED NOT NULL,
  `feedback_subject` varchar(100) NOT NULL,
  `feedback_message` text NOT NULL,
  `feedback_date` datetime NOT NULL,
  `supplier_response` text DEFAULT NULL,
  `response_date` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `agent_id`, `supplier_id`, `feedback_subject`, `feedback_message`, `feedback_date`, `supplier_response`, `response_date`, `status`) VALUES
(1, 3, 1, 'item is broken', 'please fix the item ', '2025-06-17 01:50:45', 'okey no prob. thank you for your feedback', '2025-06-17 02:47:50', 'responded');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` mediumint(8) UNSIGNED NOT NULL,
  `agent_id` mediumint(8) UNSIGNED NOT NULL,
  `product_id` mediumint(8) UNSIGNED NOT NULL,
  `orderQuantity` int(10) UNSIGNED NOT NULL,
  `custName` varchar(50) NOT NULL,
  `custAddress` varchar(70) NOT NULL,
  `custPhone` varchar(15) NOT NULL,
  `orderDate` date NOT NULL,
  `approval_status` varchar(20) DEFAULT 'pending',
  `approval_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `agent_id`, `product_id`, `orderQuantity`, `custName`, `custAddress`, `custPhone`, `orderDate`, `approval_status`, `approval_date`) VALUES
(1, 3, 1, 12, 'NIK FAREES BIN NIK FAIZAL', '18-3, Residensi RAH, 16, Jalan Raja Ali,', '01175112905', '2025-06-16', 'approved', '2025-06-16'),
(2, 3, 1, 1, 'aiman', 'rah', '123456789', '2025-06-16', 'approved', '2025-06-17'),
(3, 3, 2, 1, 'aiman', 'rah', '123456789', '2025-06-16', 'approved', '2025-06-17'),
(4, 1, 1, 3, 'Nik farees', 'No32A Jalan Flora 3f/5', '01175112905', '2025-06-17', 'approved', '2025-06-17');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` mediumint(8) UNSIGNED NOT NULL,
  `supplier_id` mediumint(8) UNSIGNED NOT NULL,
  `productName` varchar(50) NOT NULL,
  `productPrice` decimal(4,2) NOT NULL,
  `productQuantity` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `supplier_id`, `productName`, `productPrice`, `productQuantity`) VALUES
(1, 1, 'chocolate brownies', 15.50, 35),
(2, 1, 'strawberry brownies', 10.00, 19);

-- --------------------------------------------------------

--
-- Table structure for table `relationships`
--

CREATE TABLE `relationships` (
  `relationship_id` mediumint(8) UNSIGNED NOT NULL,
  `supplier_id` mediumint(8) UNSIGNED NOT NULL,
  `agent_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `relationships`
--

INSERT INTO `relationships` (`relationship_id`, `supplier_id`, `agent_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `change_id` mediumint(8) UNSIGNED NOT NULL,
  `product_id` mediumint(8) UNSIGNED NOT NULL,
  `stockquantity` int(10) UNSIGNED NOT NULL,
  `dateChange` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`change_id`, `product_id`, `stockquantity`, `dateChange`) VALUES
(1, 1, 50, '2024-09-15'),
(2, 1, 1, '2024-09-15'),
(3, 2, 20, '2025-06-16');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` mediumint(8) UNSIGNED NOT NULL,
  `supplierName` varchar(50) NOT NULL,
  `supplierEmail` varchar(40) NOT NULL,
  `supplierCategory` varchar(40) NOT NULL,
  `supplierPassword` char(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplierName`, `supplierEmail`, `supplierCategory`, `supplierPassword`) VALUES
(1, 'admin', 'admin@gmail.com', 'brownies', '40bd001563085fc35165329ea1ff5c5ecbdbbeef'),
(2, 'admin1', 'admin1@gmail.com', 'chocojars', '40bd001563085fc35165329ea1ff5c5ecbdbbeef');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`agent_id`);

--
-- Indexes for table `agents_approval`
--
ALTER TABLE `agents_approval`
  ADD PRIMARY KEY (`agent_approval_id`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `relationships`
--
ALTER TABLE `relationships`
  ADD PRIMARY KEY (`relationship_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`change_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `agent_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `agents_approval`
--
ALTER TABLE `agents_approval`
  MODIFY `agent_approval_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `relationships`
--
ALTER TABLE `relationships`
  MODIFY `relationship_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `change_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agents_approval`
--
ALTER TABLE `agents_approval`
  ADD CONSTRAINT `agents_approval_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`agent_id`),
  ADD CONSTRAINT `agents_approval_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`agent_id`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`agent_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `relationships`
--
ALTER TABLE `relationships`
  ADD CONSTRAINT `relationships_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `relationships_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`agent_id`);

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
