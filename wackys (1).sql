-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2025 at 08:41 AM
-- Server version: 11.7.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wackys`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `BookingID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `FullName` varchar(100) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `EventType` varchar(100) NOT NULL,
  `EventDate` date NOT NULL,
  `BookingDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`BookingID`, `UserID`, `FullName`, `PhoneNumber`, `Email`, `EventType`, `EventDate`, `BookingDate`) VALUES
(4, 1, 'Rue Martine', '09158361102', 'adrianorue790@gmail.com', 'Birthday Party', '2025-05-30', '2025-05-27 05:09:42');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `CartID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1,
  `DateAdded` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'Rice Combos'),
(2, 'Silog Meals'),
(3, 'Super Meals'),
(4, 'Single Orders'),
(5, 'Filipino Favorites'),
(6, 'Soup'),
(7, 'Pulutan'),
(8, 'Food Tray'),
(9, '2 in 1'),
(10, '3 in 1');

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `OrderDetailID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Subtotal` decimal(10,2) GENERATED ALWAYS AS (`Quantity` * `Price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`OrderDetailID`, `OrderID`, `ProductID`, `Quantity`, `Price`) VALUES
(1, 1, 10, 2, 140.00),
(2, 2, 10, 2, 140.00),
(3, 2, 12, 2, 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `OrderDate` datetime DEFAULT current_timestamp(),
  `TotalAmount` decimal(10,2) DEFAULT NULL,
  `OrderStatus` enum('Pending','Preparing','Cancelled','Completed') DEFAULT 'Pending',
  `PaymentMethod` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `UserID`, `OrderDate`, `TotalAmount`, `OrderStatus`, `PaymentMethod`) VALUES
(1, 1, '2025-05-27 13:08:44', 280.00, 'Completed', 'Cash on Delivery'),
(2, 1, '2025-06-05 00:20:53', 580.00, 'Completed', 'Cash on Delivery');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `isAvailable` enum('Y','N') DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image_path`, `category_id`, `isAvailable`) VALUES
(1, 'Hotsilog (Tender Juicy) (SM1)', 130.00, 'Silog meal with hotdog', 'SM1.jpg', 2, 'Y'),
(2, 'Longsilog (SM2)', 130.00, 'Silog meal with longganisa', 'SM2.jpg', 2, 'Y'),
(3, 'Tumasilog (Daing Isda) (SM3)', 160.00, 'Silog meal with daing na isda', 'SM3.jpg', 2, 'Y'),
(4, 'Tocilog (SM4)', 150.00, 'Silog meal with tocino', 'SM4.jpg', 2, 'Y'),
(5, 'Lumpiasilog (SM5)', 150.00, 'Silog meal with lumpia', 'SM5.jpg', 2, 'Y'),
(6, 'Tapsilog (SM6)', 160.00, 'Silog meal with tapa', 'SM6.jpg', 2, 'Y'),
(7, 'Porksilog (SM7)', 150.00, 'Silog meal with pork', 'SM7.jpg', 2, 'Y'),
(8, 'Chicksilog (SM8)', 140.00, 'Silog meal with chicken', 'SM8.jpg', 2, 'Y'),
(9, 'Cornsilog (SM9)', 140.00, 'Silog meal with corned beef', 'SM9.jpg', 2, 'Y'),
(10, 'Chicken Rice w/ Atchara (RC1)', 140.00, 'Rice combo with chicken and atchara', 'RC1.jpg', 1, 'Y'),
(11, 'Lumpia Rice w/ Atchara (RC2)', 150.00, 'Rice combo with lumpia and atchara', 'RC2.jpg', 1, 'Y'),
(12, 'Chicken Inasal Rice w/ Atchara (RC3)', 150.00, 'Rice combo with chicken inasal and atchara', 'RC3.jpg', 1, 'Y'),
(13, 'Liempo Inasal Rice w/ Atchara (RC4)', 150.00, 'Rice combo with liempo inasal and atchara', 'RC4.jpg', 1, 'Y'),
(14, 'Sizzling Porkchop w/ Mixed Veg. (RC5)', 180.00, 'Rice combo with sizzling porkchop and mixed vegetables', 'RC5.jpg', 1, 'Y'),
(15, 'Lumpia Rice w/ Mixed Veg. (RC6)', 160.00, 'Rice combo with lumpia and vegetables', 'RC6.jpg', 1, 'Y'),
(16, 'Sizzling Sisig w/ Rice (RC7)', 180.00, 'Rice combo with sizzling sisig', 'RC7.jpg', 1, 'Y'),
(17, 'Fried Chicken, Lumpia, Mixed Veg., Plain Rice & Mango Graham (SML1)', 220.00, 'Super meal with fried chicken and dessert', 'SML1.jpg', 3, 'Y'),
(18, 'Porkchop, Lumpia, Mixed Veg., Plain Rice & Mango Graham (SML2)', 220.00, 'Super meal with porkchop and dessert', 'SML2.jpg', 3, 'Y'),
(19, 'Chicken Inasal, Porkchop, Mixed Veg., Plain Rice & Mango Graham (SML3)', 250.00, 'Super meal with chicken and porkchop', 'SML3.jpg', 3, 'Y'),
(20, 'Liempo Inasal, Lumpia, Mixed Veg., Plain Rice & Mango Graham (SML4)', 250.00, 'Super meal with liempo and lumpia', 'SML4.jpg', 3, 'Y'),
(21, 'Spaghetti (SO1)', 60.00, 'Single order of spaghetti', 'SO1.jpg', 4, 'Y'),
(22, '6 Slices Bread Roll (SO2)', 60.00, 'Bread roll (6 slices)', 'SO2.jpg', 4, 'Y'),
(23, '1 Slice Hawaiian Pizza (SO3)', 30.00, 'One slice of Hawaiian pizza', 'SO3.jpg', 4, 'Y'),
(24, 'Buttered Toast (SO4)', 40.00, 'Single order of buttered toast', 'SO4.jpg', 4, 'Y'),
(25, 'Spaghetti w/ Pizza (SO5)', 85.00, 'Spaghetti with pizza', 'SO5.jpg', 4, 'Y'),
(26, 'Spaghetti w/ Chicken (SO6)', 125.00, 'Spaghetti with chicken', 'SO6.jpg', 4, 'Y'),
(27, 'Spaghetti, Chicken & Pizza (SO7)', 140.00, 'Combo of spaghetti, chicken, and pizza', 'SO7.jpg', 4, 'Y'),
(28, 'Spaghetti w/ 3pcs Lumpia (SO8)', 95.00, 'Spaghetti with 3 pieces lumpia', 'SO8.jpg', 4, 'Y'),
(31, 'rue', 69.00, 'sagw', 'rue.jpg', 6, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `fullName` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(500) NOT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `Role` enum('Customer','Admin') DEFAULT 'Customer',
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `fullName`, `email`, `password`, `PhoneNumber`, `Address`, `Role`, `CreatedAt`) VALUES
(1, 'Rue Martine', 'adrianorue790@gmail.com', '$2y$10$qLm/X/WUBMJf76OcX0pvo.uzGsM.9HIGsIIbWrPNuYq7KYWgIrega', '09158361102', 'Daet Camarines Norte', 'Admin', '2025-05-27 13:05:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`BookingID`),
  ADD KEY `bookings_ibfk_1` (`UserID`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`CartID`),
  ADD KEY `cart_ibfk_2` (`ProductID`),
  ADD KEY `cart_ibfk_1` (`UserID`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`OrderDetailID`),
  ADD KEY `ProductID` (`ProductID`),
  ADD KEY `orderdetails_ibfk_1` (`OrderID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `orders_ibfk_1` (`UserID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `CartID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orderdetails`
--
ALTER TABLE `orderdetails`
  MODIFY `OrderDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `products` (`id`);

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`),
  ADD CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
