-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2025 at 11:50 AM
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
-- Database: `cakeria`
--

-- --------------------------------------------------------

--
-- Table structure for table `cakes`
--

CREATE TABLE `cakes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cakes`
--

INSERT INTO `cakes` (`id`, `name`, `description`, `price`, `image`) VALUES
(1, 'Chocolate Truffle Cake', 'Rich and moist chocolate cake with layers of truffle filling.', 1600.00, 'images/chocolate_truffle.jpg'),
(2, 'Vanilla Delight Cake', 'Classic vanilla cake with creamy frosting and a touch of elegance.', 1200.00, 'images/678fd8127ca26.jpg'),
(3, 'Red Velvet Cake', 'Velvety smooth red velvet cake with a hint of cocoa and cream cheese frosting.', 1800.00, 'images/red_velvet.jpg'),
(4, 'Black Forest Cake', 'A delightful combination of chocolate, cherries, and whipped cream.', 1600.00, 'images/6790923a2998f.jpeg'),
(5, 'Fruit Cake', 'A moist and flavorful cake loaded with a variety of fresh fruits.', 1400.00, 'images/fruit_cake.jpg'),
(6, 'Butter Scotch Cake', 'Delicious butterscotch cake with caramel glaze and crunchy nuts.', 1700.00, 'images/butter_scotch.jpg'),
(7, 'Pineapple Cake', 'Light and refreshing pineapple cake with layers of pineapple filling.', 1300.00, 'images/pineapple_cake.jpg'),
(8, 'Mango Cake', 'Tropical mango cake with creamy mango frosting and fresh mango slices.', 1500.00, 'images/mango_cake.jpg'),
(9, 'Strawberry Cake', 'Sweet and tangy strawberry cake with layers of fresh strawberry filling.', 1600.00, 'images/strawberry_cake.jpg'),
(10, 'Coffee Walnut Cake', 'A rich and moist coffee cake with crunchy walnut pieces.', 1800.00, 'images/coffee_walnut.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cake_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`) VALUES
(1, 'Rijan Shrestha', 'rijanstha@gmail.com'),
(2, 'rijanstha', 'rijan20@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `cake_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `cake_id`, `quantity`, `total_price`, `status`, `order_date`) VALUES
(7, 1, 1, 1, 1600.00, 'pending', '2025-01-22 15:03:03'),
(8, 1, 1, 1, 1600.00, 'pending', '2025-01-23 05:58:37'),
(9, 1, 2, 1, 2800.00, 'pending', '2025-01-23 05:58:37'),
(10, 1, 1, 1, 4400.00, 'pending', '2025-01-23 05:58:37'),
(11, 1, 1, 1, 6000.00, 'pending', '2025-01-23 05:58:37'),
(12, 2, 1, 1, 1600.00, 'pending', '2025-02-19 18:16:42'),
(13, 2, 1, 1, 1600.00, 'completed', '2025-02-19 18:16:56'),
(15, 2, 1, 1, 1600.00, 'pending', '2025-02-20 09:28:25'),
(16, 2, 6, 1, 1700.00, 'pending', '2025-02-20 13:41:25'),
(17, 1, 1, 1, 1600.00, 'pending', '2025-02-20 13:42:08'),
(18, 2, 1, 1, 1600.00, 'pending', '2025-02-20 13:51:10'),
(19, 2, 1, 1, 1600.00, 'pending', '2025-02-21 06:25:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`) VALUES
(3, 'Rijan Shrestha', 'rijanstha@gmail.com', '$2y$10$YeuOELHrDLo22aPzArnKXe4KP7aGQKt9YQnBsYccPdbPjJxLLMcLq', NULL),
(5, 'Abhi', '', '$2y$10$cGY0UdKZgS6JbTAlU/CPT.5lEWnTGoOvft3sn6YiWdkLgPpV9AsUC', 'admin'),
(11, 'Rijan Shrestha', 'rijansthataujale16@gmail.com', '$2y$10$4KKSeWZEw9W57Xn3/X8/S.H3R.H7GX7TULAPonXo5UQ3/WAhL4sFy', NULL),
(12, 'rijanstha', 'rijan20@gmail.com', '$2y$10$U8WkSUdSdjbiXLjJxcl7ue4hQfzZ3rcx26MlinfeZJTvp5/Uu7qpy', NULL),
(13, '+-1234', '123@100.com', '$2y$10$sQWvIT2ALKGS4ZSfysrInuzoxrlAvHjO6Hgb6WpC3k9juQACAzTt2', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cakes`
--
ALTER TABLE `cakes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cake_id` (`cake_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `cake_id` (`cake_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cakes`
--
ALTER TABLE `cakes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`cake_id`) REFERENCES `cakes` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`cake_id`) REFERENCES `cakes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
