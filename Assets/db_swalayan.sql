-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 06:21 AM
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
-- Database: `db_swalayan`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `Id` int(11) NOT NULL,
  `Name` varchar(225) NOT NULL,
  `Email` varchar(225) NOT NULL,
  `Role` enum('Admin','Cashier') NOT NULL,
  `Password` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`Id`, `Name`, `Email`, `Role`, `Password`) VALUES
(1, 'Mukti', 'mukti@mail.com', 'Admin', '123'),
(2, 'Yanuar', 'yanuar@mail.com', 'Cashier', '123');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `Id` int(11) NOT NULL,
  `Name` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`Id`, `Name`) VALUES
(1, 'Food'),
(2, 'Drink\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `Id` int(11) NOT NULL,
  `Name` varchar(225) NOT NULL,
  `Price` double(10,2) NOT NULL,
  `Stock` int(225) NOT NULL,
  `Harga_Jual` double(10,2) NOT NULL,
  `Date_Added` date NOT NULL,
  `Expired_Date` date NOT NULL,
  `Category_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`Id`, `Name`, `Price`, `Stock`, `Harga_Jual`, `Date_Added`, `Expired_Date`, `Category_Id`) VALUES
(5, 'Indomie Goreng Spesial', 3500.00, 15, 3850.00, '2025-06-03', '2026-06-03', 1),
(6, 'Chitato Sapi Panggang 68g', 10000.00, 50, 11000.00, '2025-06-03', '2026-06-03', 1),
(7, 'Roti Tawar Sari Roti', 15000.00, 29, 16500.00, '2025-06-03', '2026-06-03', 1),
(8, 'Teh Botol Sosro 350ml', 5000.00, 80, 5500.00, '2025-06-03', '2026-06-03', 2),
(9, 'Aqua 600ml', 4000.00, 120, 4400.00, '2025-06-03', '2026-06-03', 2),
(10, 'Ultra Milk Cokelat 250ml', 6000.00, 60, 6600.00, '2025-06-03', '2026-06-03', 2),
(12, 'Susu UHT 1L', 18000.00, 20, 19800.00, '2025-06-03', '2027-06-10', 2);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `Id` int(11) NOT NULL,
  `Transaction_Date` date NOT NULL,
  `Subtotal` double(10,2) NOT NULL,
  `PPN` double(10,2) NOT NULL,
  `Total` double(10,2) NOT NULL,
  `Money_Paid` double(10,2) NOT NULL,
  `Change` double(10,2) NOT NULL,
  `Employee_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`Id`, `Transaction_Date`, `Subtotal`, `PPN`, `Total`, `Money_Paid`, `Change`, `Employee_Id`) VALUES
(2, '2025-06-03', 10500.00, 1260.00, 11760.00, 20000.00, 8240.00, 2),
(3, '2025-06-03', 15000.00, 1800.00, 16800.00, 20000.00, 3200.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `Id` int(11) NOT NULL,
  `Quantity` int(225) NOT NULL,
  `Subtotal` double(10,2) NOT NULL,
  `Product_Id` int(11) NOT NULL,
  `Transaction_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO `transaction_details` (`Id`, `Quantity`, `Subtotal`, `Product_Id`, `Transaction_Id`) VALUES
(1, 3, 10500.00, 5, 2),
(2, 1, 15000.00, 7, 3);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `Id` int(11) NOT NULL,
  `Price` double(10,2) NOT NULL,
  `Harga_Jual` double(10,2) NOT NULL,
  `Stock` int(225) NOT NULL,
  `Date_Added` date NOT NULL,
  `Expired_Date` date NOT NULL,
  `Product_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`Id`, `Price`, `Harga_Jual`, `Stock`, `Date_Added`, `Expired_Date`, `Product_Id`) VALUES
(1, 10000.00, 11000.00, 20, '2025-06-03', '2026-11-25', 6);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Category_Id` (`Category_Id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `User_Id` (`Employee_Id`),
  ADD KEY `Employee_Id` (`Employee_Id`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Product_Id` (`Product_Id`),
  ADD KEY `Transaction_Id` (`Transaction_Id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Product_Id` (`Product_Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`Category_Id`) REFERENCES `categories` (`Id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`Employee_Id`) REFERENCES `accounts` (`Id`);

--
-- Constraints for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_ibfk_1` FOREIGN KEY (`Product_Id`) REFERENCES `products` (`Id`),
  ADD CONSTRAINT `transaction_details_ibfk_2` FOREIGN KEY (`Transaction_Id`) REFERENCES `transactions` (`Id`);

--
-- Constraints for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD CONSTRAINT `warehouses_ibfk_1` FOREIGN KEY (`Product_Id`) REFERENCES `products` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
