-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2024 at 05:39 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exam_hall`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`email`, `password`) VALUES
('Admin@gmail.com', '$2y$10$7ifyK9BUOtC.Ae6S8Q7Ui.eIDyKXA8aID1lI7j6nv.TvoXNH/t0XO');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_number` varchar(50) NOT NULL,
  `seats` int(11) NOT NULL,
  `is_occupied` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_number`, `seats`, `is_occupied`) VALUES
('307', 20, 0),
('308', 50, 0),
('A 305', 30, 0),
('A 306', 30, 0);

-- --------------------------------------------------------

--
-- Table structure for table `room_allocations`
--

CREATE TABLE `room_allocations` (
  `id` int(11) NOT NULL,
  `room_number` varchar(255) NOT NULL,
  `seats` int(11) NOT NULL,
  `start_register_number` varchar(255) NOT NULL,
  `end_register_number` varchar(255) NOT NULL,
  `dept` varchar(255) NOT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `room_allocations`
--

INSERT INTO `room_allocations` (`id`, `room_number`, `seats`, `start_register_number`, `end_register_number`, `dept`, `year`) VALUES
(3, 'A 305', 0, '2422K1829', '2422K1840', 'Computer Science', 1);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `reg_no` varchar(255) NOT NULL,
  `email` varchar(20) NOT NULL,
  `dept` varchar(255) NOT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `reg_no`, `email`, `dept`, `year`) VALUES
(1, '2422K1829', 'sathish@gmail.com', 'Computer Science', 1),
(2, '2422K1830', '', 'Computer Science', 1),
(3, '2422K1831', '', 'Computer Science', 1),
(4, '2422K1832', '', 'Computer Science', 1),
(5, '2422K1833', '', 'Computer Science', 1),
(6, '2422K1834', '', 'Computer Science', 1),
(7, '2422K1835', '', 'Computer Science', 1),
(8, '2422K1836', '', 'Computer Science', 1),
(9, '2422K1837', '', 'Computer Science', 1),
(10, '2422K1838', '', 'Computer Science', 1),
(11, '2422K1839', '', 'Computer Science', 1),
(12, '2422K1840', '', 'Computer Science', 1),
(13, '2422K1841', '', 'Computer Science', 1),
(14, '2422K1842', '', 'Computer Science', 1),
(15, '2422K1843', '', 'Computer Science', 1),
(16, '2422K1844', '', 'Computer Science', 1),
(17, '2422K1845', '', 'Computer Science', 1),
(18, '2422K1846', '', 'Computer Science', 1),
(19, '2422K1847', '', 'Computer Science', 1),
(20, '2422K1848', '', 'Computer Science', 1),
(21, '2422K1849', '', 'Computer Science', 1),
(22, '2422K1850', '', 'Computer Science', 1),
(23, '2422K1851', '', 'Computer Science', 1),
(24, '2422K1852', '', 'Computer Science', 1),
(25, '2422K1853', '', 'Computer Science', 1),
(26, '2422K1854', '', 'Computer Science', 1),
(27, '2422K1855', '', 'Computer Science', 1),
(28, '2422K2831', '', 'Computer Science', 1),
(29, '2422K2832', '', 'Computer Science', 1);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `room_number` varchar(20) DEFAULT NULL,
  `staff_id` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `teacher_name`, `room_number`, `staff_id`, `email`, `password`) VALUES
(28, 'Surya', 'A 305', 'Acas01', 'Surya@gmail.com', '$2y$10$FtpvLqrSifh8K'),
(29, 'Krishna', '307', 'Acas02', 'Krishna@gmail.com', '$2y$10$Bixdk8VYKVSZ0');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('teacher','student','admin') NOT NULL,
  `reg_no` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `username`, `password`, `role`, `reg_no`, `created_at`) VALUES
(1, 'Sathish@gmail.com', 'Sathish', '$2y$10$OPLIytdr.AOU5IoIID/eJ.EyvVirsEb3bR3WXgPiGAdDfIiKdyoGC', 'student', '', '2024-11-30 18:09:27'),
(2, 'Krishna@gmail.com', 'Krishnaraj', '$2y$10$Q9kYe2fN1RWpTWZY.XvDUOikCCAVgNNNkrvWDhYNuA5x0yG2IOsTW', 'admin', '', '2024-11-30 18:10:13'),
(3, 'Krishnaveni@gmail.com', 'Krishnaveni', '$2y$10$8CPg2tXV3oW6d4Gsrdu1gOg7XhMHHB9HKq16K67KNPPAunK.O/Eai', 'teacher', '', '2024-11-30 18:10:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_number`) USING BTREE;

--
-- Indexes for table `room_allocations`
--
ALTER TABLE `room_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_number` (`room_number`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`,`reg_no`) USING BTREE;

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD KEY `room_number` (`room_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `room_allocations`
--
ALTER TABLE `room_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `room_allocations`
--
ALTER TABLE `room_allocations`
  ADD CONSTRAINT `room_allocations_ibfk_1` FOREIGN KEY (`room_number`) REFERENCES `rooms` (`room_number`) ON DELETE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`room_number`) REFERENCES `rooms` (`room_number`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
