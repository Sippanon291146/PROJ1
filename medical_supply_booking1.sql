-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 21, 2025 at 06:08 PM
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
-- Database: `medical_supply_booking1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `fullname`) VALUES
(1, 'admin', '$2y$10$gVFBhOlTXPa1b4t.sUPcH.anPApoAFP2JXO3d2apIyBtaN8IFbSvK', NULL),
(2, 'admin2', '$2y$10$r60KmQv/v6LNNXAxtBRQyuZjtikfaaO9YUCbDeHw.RAM6mdjr2vWm', 'admin2');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(1, 3, 1, 1, '2025-06-20 16:00:31');

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `issue_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('รอรับเรื่อง','กำลังดำเนินการ','เสร็จสิ้น') DEFAULT 'รอรับเรื่อง',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`issue_id`, `user_id`, `title`, `description`, `status`, `created_at`) VALUES
(1, 1, 'ข้อความจากผู้ใช้ admin', 'เกิดบัค', 'เสร็จสิ้น', '2025-06-21 13:58:27'),
(2, 1, 'ข้อความจากผู้ใช้ admin', 'ไม่แจ้งเตือน', 'เสร็จสิ้น', '2025-06-21 14:06:24'),
(3, 3, 'ข้อความจากผู้ใช้ sippanon', 'สนาะไม่อัพเดต', 'เสร็จสิ้น', '2025-06-21 14:08:08'),
(4, 3, 'ข้อความจากผู้ใช้ sippanon', 'บัค', 'เสร็จสิ้น', '2025-06-21 17:34:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'รอดำเนินการ',
  `appointment_date` date DEFAULT NULL,
  `prescription_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_notified` tinyint(1) DEFAULT 0,
  `payment_status` varchar(50) DEFAULT 'รอดำเนินการ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `status`, `appointment_date`, `prescription_file`, `created_at`, `is_notified`, `payment_status`) VALUES
(1, 3, 90.00, 'อนุมัติ', '2025-06-27', '', '2025-06-20 16:03:22', 1, 'รอดำเนินการ'),
(2, 3, 90.00, 'ยกเลิก', '2025-06-22', '', '2025-06-20 17:00:42', 1, 'รอดำเนินการ'),
(3, 3, NULL, 'อนุมัติ', '2025-06-28', NULL, '2025-06-20 17:06:54', 1, 'ชำระเงินเรียบร้อย'),
(4, 3, NULL, 'อนุมัติ', '2025-06-21', NULL, '2025-06-20 17:23:29', 1, 'รอดำเนินการ'),
(5, 3, NULL, 'อนุมัติ', '2025-06-29', NULL, '2025-06-20 17:24:29', 1, 'รอดำเนินการ'),
(6, 3, NULL, 'อนุมัติ', '2025-06-29', NULL, '2025-06-20 17:26:53', 1, 'รอดำเนินการ'),
(7, 3, NULL, 'อนุมัติ', '2025-06-27', NULL, '2025-06-20 17:29:41', 1, 'รอดำเนินการ'),
(8, 3, NULL, 'อนุมัติ', '2025-06-26', NULL, '2025-06-21 05:33:55', 1, 'รอดำเนินการ'),
(9, 3, NULL, 'อนุมัติ', '2025-06-27', NULL, '2025-06-21 07:02:04', 1, 'รอดำเนินการ'),
(10, 3, NULL, 'อนุมัติ', '2025-06-27', NULL, '2025-06-21 07:07:23', 1, 'รอดำเนินการ'),
(11, 3, NULL, 'อนุมัติ', '2025-06-22', NULL, '2025-06-21 13:53:38', 1, 'ชำระเงินเรียบร้อย'),
(12, 3, NULL, 'อนุมัติ', '2025-06-22', NULL, '2025-06-21 14:09:15', 1, 'รอดำเนินการ'),
(13, 1, NULL, 'อนุมัติ', '2025-06-29', NULL, '2025-06-21 14:40:35', 1, 'ชำระเงินเรียบร้อย'),
(14, 1, NULL, 'อนุมัติ', '2025-06-22', NULL, '2025-06-21 14:55:09', 1, 'ชำระเงินเรียบร้อย');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, NULL, 1, 90.00),
(2, 2, NULL, 1, 90.00),
(3, 3, 1, 1, 90.00),
(4, 4, 1, 1, 90.00),
(5, 5, 1, 1, 90.00),
(6, 6, 1, 2, 90.00),
(7, 7, 1, 2, 90.00),
(8, 8, 1, 1, 90.00),
(9, 9, 2, 1, 150.00),
(10, 10, 2, 1, 150.00),
(11, 11, 1, 1, 90.00),
(12, 12, 3, 1, 20.00),
(13, 13, 3, 1, 20.00),
(14, 14, 2, 2, 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `payment_date` datetime NOT NULL,
  `slip_file` varchar(255) DEFAULT NULL,
  `payment_method` varchar(20) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'รอตรวจสอบ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_date`, `slip_file`, `payment_method`, `amount`, `paid_at`, `status`) VALUES
(1, 1, '2025-06-20 01:05:00', '6855874d44e46.jpg', '', NULL, '2025-06-20 16:07:41', 'รอตรวจสอบ'),
(2, 3, '2025-06-20 00:11:00', NULL, 'cash', NULL, '2025-06-20 17:12:52', 'รอตรวจสอบ'),
(3, 5, '2025-06-20 00:24:00', NULL, 'cash', NULL, '2025-06-20 17:24:47', 'รอตรวจสอบ'),
(4, 6, '2025-06-20 00:27:00', NULL, 'cash', NULL, '2025-06-20 17:27:10', 'รอตรวจสอบ'),
(5, 7, '2025-06-20 00:29:00', NULL, 'cash', NULL, '2025-06-20 17:29:48', 'รอตรวจสอบ'),
(6, 8, '2025-06-21 12:37:00', NULL, 'cash', NULL, '2025-06-21 05:37:15', 'รอตรวจสอบ'),
(7, 9, '2025-06-21 14:02:00', NULL, 'cash', NULL, '2025-06-21 07:02:12', 'รอตรวจสอบ'),
(8, 10, '2025-06-21 14:07:00', NULL, 'cash', NULL, '2025-06-21 07:07:35', 'รอตรวจสอบ'),
(9, 11, '2025-06-21 20:53:00', NULL, 'cash', NULL, '2025-06-21 13:54:15', 'รอตรวจสอบ'),
(10, 13, '2025-06-21 21:40:00', '6856c50936712.jpg', 'qr', NULL, '2025-06-21 14:43:21', 'รอตรวจสอบ'),
(11, 14, '2025-06-21 21:55:00', NULL, 'cash', NULL, '2025-06-21 14:56:06', 'รอตรวจสอบ');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `prescription_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `product_type` varchar(50) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `price`, `stock`, `category`, `product_type`, `img`, `created_at`) VALUES
(1, 'tylenol', 'ช่วยลดไข้ ลดอาการปวด', 90.00, 10, 'ยาสามัญ', '1', '68558bf3f3938.jpg', '2025-06-20 15:53:12'),
(2, 'วิตามิน C', 'ช่วยสร้างภูมิคุ้มกัน', 150.00, 20, 'วิตตามิน', '2', '68564b2632b3d.png', '2025-06-21 05:59:36'),
(3, 'ยาแก้ไอน้ำดำ', 'ช่วยบรรเทาอาการไอ', 20.00, 50, 'ยาสามัญ', '1', '68564b19b08fc.png', '2025-06-21 06:02:54');

-- --------------------------------------------------------

--
-- Table structure for table `qr_codes`
--

CREATE TABLE `qr_codes` (
  `id` int(11) NOT NULL,
  `qr_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `fullname`, `email`, `phone`, `created_at`) VALUES
(1, 'admin', '$2y$10$r1Q5aiBrglGBsDiqgV5yQOH4bmv.zLYWMiDCSNMqEk/iMG2WtYWhK', 'admin', 'admin1@admin.com', '0926826529', '2025-06-20 15:03:23'),
(2, 'admin1', '$2y$10$dWkQGAxj/4veZQ8wXSAmqe2xfnzg.oT8ja5gEq9/CtW4VGS1ryqq6', 'admin1', 'admin@hotmail.com', '0926826529', '2025-06-20 15:07:45'),
(3, 'sippanon', '$2y$10$6r7eRwaAM6o6V0bw.k12H.XxtEyhE1RhHB7cmxXwXn738mVYsgBJi', 'sippanon test', 'sippanontest@test.com', '0123456789', '2025-06-20 15:16:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`issue_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `qr_codes`
--
ALTER TABLE `qr_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `qr_codes`
--
ALTER TABLE `qr_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
