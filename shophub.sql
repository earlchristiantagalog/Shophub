-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2025 at 03:31 PM
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
-- Database: `shophub`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address_line_1` text DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `user_id`, `first_name`, `last_name`, `phone`, `address_line_1`, `barangay`, `city`, `province`, `region`, `zip_code`, `is_default`) VALUES
(5, 4, 'Earl Christian', 'Tagalog', '09168218393', 'adsadasdsad\r\nsadsadsadsadsadsda', 'Pulpogan', 'manila', 'Texas', 'Luzon', '4555', 1),
(9, 9, 'Earl Christian', 'Tagalog', '099234567890', 'Malunhaw', 'Pulpogan', 'Consolacion', 'Cebu', 'Central Visayas', '6001', 1),
(10, 10, 'Earl Christian', 'Tagalog', '099234567890', 'asdasdsdsd', 'Tejero', 'City of Cebu', 'Cebu', 'Central Visayas', '6001', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `admin_id` varchar(15) NOT NULL,
  `full_name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(191) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `admin_id`, `full_name`, `email`, `password`, `created_at`) VALUES
(2, '782346-617461', 'Admin Test', 'lirafren30@gmail.com', '12345678', '2025-07-05 07:42:57'),
(3, '323158-930082', 'Earl Christian Tagalog', 'earlchristiantagalog10@gmail.com', '1234567890', '2025-07-05 16:56:09');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `variant_type` varchar(100) DEFAULT NULL,
  `variant_value` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_variants`
--

CREATE TABLE `cart_variants` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `variant_type` varchar(100) DEFAULT NULL,
  `variant_value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(4, 'PC', '2025-08-06 01:49:53');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_receipts`
--

CREATE TABLE `delivery_receipts` (
  `id` int(11) NOT NULL,
  `receipt_id` varchar(20) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) DEFAULT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `promo_discount` decimal(10,2) DEFAULT NULL,
  `shipping_method` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(155) NOT NULL,
  `remarks` text DEFAULT 'Order Placed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `address_id`, `total`, `shipping_fee`, `promo_code`, `promo_discount`, `shipping_method`, `payment_method`, `order_date`, `subtotal`, `status`, `remarks`) VALUES
('ES51987', 10, 10, 550.00, 100.00, '0', 50.00, 'Express Delivery', 'COD', '2025-10-21 15:52:37', 500.00, 'Cancelled', 'Order cancelled by admin'),
('ES59156', 10, 10, 1250.00, 100.00, '0', 100.00, 'Express Delivery', 'Credit/Debit Card', '2025-10-22 02:10:40', 1250.00, 'Processing', 'Order received and processing'),
('ES85649', 10, 10, 1000.00, 100.00, '0', 100.00, 'Express Delivery', 'GCash', '2025-10-21 14:54:32', 1000.00, 'Cancelled', 'Order cancelled by seller');

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `after_order_update` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
    DECLARE changed INT DEFAULT 0;

    -- Insert tracking only if remarks or order_status changed
    IF NEW.remarks <> OLD.remarks OR NEW.status <> OLD.status THEN
        INSERT INTO order_tracking (order_id, status, remarks)
        VALUES (NEW.order_id, NEW.status, NEW.remarks);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_codes`
--

CREATE TABLE `order_codes` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `barcode_filename` varchar(255) NOT NULL,
  `qrcode_filename` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `reviewed` tinyint(1) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_image`, `quantity`, `reviewed`, `price`, `subtotal`, `created_at`) VALUES
(104, 'ES85649', 7, 'Premium Wireless Headphones', 'uploads/img_6892b4f48ddc06.27583354.png', 5, 0, 200.00, 1000.00, '2025-10-21 14:54:32'),
(105, 'ES51987', 7, 'Premium Wireless Headphones', 'uploads/img_6892b4f48ddc06.27583354.png', 2, 0, 250.00, 500.00, '2025-10-21 15:52:37'),
(106, 'ES59156', 7, 'Premium Wireless Headphones', 'uploads/img_6892b4f48ddc06.27583354.png', 5, 0, 250.00, 1250.00, '2025-10-22 02:10:40');

-- --------------------------------------------------------

--
-- Table structure for table `order_item_variants`
--

CREATE TABLE `order_item_variants` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_type` varchar(100) NOT NULL,
  `variant_value` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item_variants`
--

INSERT INTO `order_item_variants` (`id`, `order_id`, `product_id`, `variant_type`, `variant_value`, `created_at`) VALUES
(142, 'ES85649', 7, 'size', 'Small', '2025-10-21 14:54:32'),
(143, 'ES51987', 7, 'size', 'Small', '2025-10-21 15:52:37'),
(144, 'ES59156', 7, 'size', 'Small', '2025-10-22 02:10:40');

-- --------------------------------------------------------

--
-- Table structure for table `order_tracking`
--

CREATE TABLE `order_tracking` (
  `track_id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_tracking`
--

INSERT INTO `order_tracking` (`track_id`, `order_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(99, 'ES85649', 'Accepted', 'Order Placed', '2025-10-21 22:59:05', '2025-10-21 22:59:05'),
(100, 'ES85649', 'Processing', 'Order Placed', '2025-10-21 22:59:20', '2025-10-21 22:59:20'),
(101, 'ES85649', 'Shipped', 'Order Placed', '2025-10-21 23:09:33', '2025-10-21 23:09:33'),
(102, 'ES85649', 'Out for Delivery', 'Order Placed', '2025-10-21 23:17:37', '2025-10-21 23:17:37'),
(103, 'ES85649', 'Processing', 'Order Placed', '2025-10-21 23:20:42', '2025-10-21 23:20:42'),
(104, 'ES85649', 'Shipped', 'Order Placed', '2025-10-21 23:21:46', '2025-10-21 23:21:46'),
(105, 'ES85649', 'Out for Delivery', 'Order Placed', '2025-10-21 23:28:49', '2025-10-21 23:28:49'),
(106, 'ES85649', 'Delivered', 'Order Placed', '2025-10-21 23:29:04', '2025-10-21 23:29:04'),
(107, 'ES85649', 'Shipped', 'Order Placed', '2025-10-21 23:48:10', '2025-10-21 23:48:10'),
(108, 'ES85649', 'Shipped', 'Item has been shipped', '2025-10-21 23:48:28', '2025-10-21 23:48:28'),
(109, 'ES51987', 'Cancelled', 'Order cancelled by admin', '2025-10-21 23:58:40', '2025-10-21 23:58:40'),
(110, 'ES51987', 'Cancelled', 'Order cancelled by admin', '2025-10-21 23:58:40', '2025-10-21 23:58:40'),
(111, 'ES85649', 'Cancelled', 'Order cancelled by seller', '2025-10-22 09:44:00', '2025-10-22 09:44:00'),
(112, 'ES59156', 'Accepted', 'Order Placed', '2025-10-22 10:12:15', '2025-10-22 10:12:15'),
(113, 'ES59156', 'Processing', 'Order received and processing', '2025-10-22 10:13:08', '2025-10-22 10:13:08');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sold` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `price`, `stock`, `category`, `status`, `description`, `created_at`, `sold`) VALUES
(7, 'Premium Wireless Headphones', 250.00, 138, 'PC', 'active', 'awdasdasdsadasd\r\nsadsadsadasdasdas\r\ndasdsdasdasdasdas\r\ndasdasdasdasasda\r\ndasdasdasdasdasdasd\r\nasdasdsdasdasdsadas\r\ndasdsadasdasdasdsadad', '2025-08-06 01:50:44', 62),
(8, 'Ethernet Lan Cable', 5.00, 970, 'PC', 'inactive', 'Cat5e Ethernet Cable\r\n\r\nUp to 1 Gbps\r\n\r\nBandwidth: 100 MHz\r\n\r\nDistance: Reliable up to 100m\r\n\r\nPerfect for: Home & office networks, browsing, streaming, light gaming\r\n\r\nCat6 Ethernet Cable\r\n\r\nSpeed: Up to 10 Gbps\r\n\r\nBandwidth: 250 MHz\r\n\r\nEnhanced shielding, reduced interference\r\n\r\nPerfect for: Gaming, 4K/8K streaming, business & enterprise networks', '2025-08-16 04:01:06', 30),
(9, 'asedsddsdsdsdsd', 45.00, 3432, 'PC', 'inactive', 'adasdasdasdasdasd\r\nsdsdsdasdasdsdas\r\nsdasdasdasdasdasd\r\nsdsdsadsadasddasd', '2025-10-20 10:29:32', 2);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_path`, `is_primary`) VALUES
(86, 7, 'uploads/img_6892b4f48ddc06.27583354.png', 1),
(87, 7, 'uploads/img_6892b4f48fdde4.49498966.jpg', 0),
(88, 7, 'uploads/img_6892b4f491d937.12570892.jpg', 0),
(89, 7, 'uploads/img_6892b4f4947828.14656847.jfif', 0),
(90, 7, 'uploads/img_6892b4f4955891.38727019.jfif', 0),
(91, 8, 'uploads/img_68a00282d33814.44390317.jfif', 1),
(92, 8, 'uploads/img_68a00282d42b08.38437134.jfif', 0),
(93, 8, 'uploads/img_68a00282d575e8.66323386.jfif', 0),
(94, 8, 'uploads/img_68a00282d66f69.61443725.jpg', 0),
(95, 8, 'uploads/img_68a00282d84526.13828351.jpg', 0),
(96, 9, 'uploads/img_68f60f0ca87131.40254087.png', 1),
(97, 9, 'uploads/img_68f60f0caac5e3.12293513.jpg', 0),
(98, 9, 'uploads/img_68f60f0cab6d58.02814583.png', 0),
(99, 9, 'uploads/img_68f60f0cac34b2.56978452.jpg', 0),
(100, 9, 'uploads/img_68f60f0cae53a6.84459213.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_type` varchar(100) NOT NULL,
  `variant_value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `variant_type`, `variant_value`) VALUES
(2, 4, 'Type', 'Cat5'),
(3, 4, 'Type', 'Cat5e'),
(4, 4, 'Type', 'Cat6'),
(5, 4, 'Meter', '10'),
(6, 4, 'Meter', '20'),
(7, 4, 'Meter', '30'),
(8, 4, 'Color', 'Gray'),
(9, 4, 'Color', 'Blue'),
(10, 5, 'Color', 'Pink'),
(11, 5, 'Color', 'Blue'),
(12, 5, 'Color', 'Red'),
(13, 5, 'Size', 'Small'),
(14, 5, 'Size', 'Medium'),
(15, 5, 'Size', 'Large'),
(0, 7, 'Size', 'Small'),
(0, 7, 'Size', 'Medium'),
(0, 7, 'Size', 'Large'),
(0, 8, 'Category', 'Cat5e'),
(0, 8, 'Category', 'Cat6'),
(0, 8, 'Meter', '5'),
(0, 8, 'Meter', '10'),
(0, 8, 'Meter', '15'),
(0, 8, 'Meter', '20'),
(0, 8, 'Color', 'Gray'),
(0, 8, 'Color', 'Blue'),
(0, 9, 'Color', 'Red'),
(0, 9, 'Color', 'Blue'),
(0, 9, 'Color', 'Gray');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `order_id`, `product_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(9, 0, 7, 4, 5, 'THe prodahsdjasdasjdbsajdbsad', '2025-08-16 10:12:39'),
(10, 0, 8, 9, 5, 'tesfsdfdsf', '2025-10-20 18:47:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `account_no` varchar(15) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_code` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `account_no`, `username`, `email`, `phone`, `password`, `created_at`, `verification_code`, `is_verified`) VALUES
(4, '', 'earlchrisss', 'earlchristian@example.com', '', '$2y$10$EgnRSmgsw0IcRrX1Czot0u4Ip0G/JX1PK2oGn.3Sl49agpkX8.SgO', '2025-08-06 01:49:04', NULL, 0),
(9, '33186', 'Earlchriss', 'earlchristiantagalog1@gmail.com', '099234567890', '$2y$10$Hv9oe8Dio3d0ORNA6eHpPeW2EWtFMLnF4WvXB9yS8uo4xmrB3J6Q.', '2025-10-20 09:58:04', '460794', 1),
(10, '19139', 'Earr', 'earlchristiantagalog10@gmail.com', '099234567890', '$2y$10$EmNJYEdi1AwWtnCsSlb32.cie8D819ayZ0WI4Xs5ZW6baVELem0oC', '2025-10-20 10:34:12', '401218', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount` int(11) NOT NULL,
  `expiry` date NOT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `status` enum('Active','Expired') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `discount`, `expiry`, `is_used`, `status`, `created_at`) VALUES
(3, 'ESISFREE', 150, '2025-08-28', 0, 'Active', '2025-08-15 02:24:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `cart_variants`
--
ALTER TABLE `cart_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `delivery_receipts`
--
ALTER TABLE `delivery_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_id` (`receipt_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `order_codes`
--
ALTER TABLE `order_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_item_variants`
--
ALTER TABLE `order_item_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD PRIMARY KEY (`track_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `reviews_ibfk_1` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `cart_variants`
--
ALTER TABLE `cart_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `delivery_receipts`
--
ALTER TABLE `delivery_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_codes`
--
ALTER TABLE `order_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `order_item_variants`
--
ALTER TABLE `order_item_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `order_tracking`
--
ALTER TABLE `order_tracking`
  MODIFY `track_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_variants`
--
ALTER TABLE `cart_variants`
  ADD CONSTRAINT `cart_variants_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_item_variants`
--
ALTER TABLE `order_item_variants`
  ADD CONSTRAINT `order_item_variants_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_variants_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD CONSTRAINT `order_tracking_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
