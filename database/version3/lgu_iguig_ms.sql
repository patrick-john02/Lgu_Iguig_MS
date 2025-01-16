-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2025 at 04:24 AM
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
-- Database: `lgu_iguig_ms`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminlogs`
--

CREATE TABLE `adminlogs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action_type` enum('Add','Edit','Delete','Approve','Reject') NOT NULL,
  `target_table` enum('Document','Event','User') NOT NULL,
  `target_id` int(11) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminlogs`
--

INSERT INTO `adminlogs` (`id`, `admin_id`, `action_type`, `target_table`, `target_id`, `timestamp`) VALUES
(1, 1, 'Add', 'Document', 1, '2025-01-13 12:58:33');

-- --------------------------------------------------------

--
-- Table structure for table `archiveddocuments`
--

CREATE TABLE `archiveddocuments` (
  `id` int(11) NOT NULL,
  `document_id` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `document_type` enum('Memorandum','Resolution','Ordinance') NOT NULL,
  `authored_by` int(11) NOT NULL,
  `date` date NOT NULL,
  `subject` text NOT NULL,
  `description` text NOT NULL,
  `rejection_reason` text DEFAULT NULL,
  `archived_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carouselcontent`
--

CREATE TABLE `carouselcontent` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `caption` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carouselcontent`
--

INSERT INTO `carouselcontent` (`id`, `image`, `caption`, `link`, `created_at`) VALUES
(1, '../uploads/kasal.jpg', 'Image caption', 'https://www.facebook.com/profile.php?id=100066604785144', '2025-01-13 12:58:33'),
(2, '../uploads/gunban.jpg', 'Gun Ban Election', 'https://github.com/patrick-john02/LTAS', '2025-01-14 02:31:01'),
(3, '../uploads/kasal.jpg', 'kasal', 'https://github.com/patrick-john02/LTAS', '2025-01-14 02:31:18');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `description`) VALUES
(1, 'Finance', 'Handles all financial activities including budgeting and accounting.'),
(2, 'Human Resources', 'Manages employee relations, payroll, and recruitment.'),
(3, 'IT Department', 'Responsible for managing the company’s technology infrastructure.');

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `id` int(11) NOT NULL,
  `document_type` enum('Memorandum','Resolution','Ordinance') NOT NULL,
  `document_id` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `authored_by` int(11) NOT NULL,
  `date` date NOT NULL,
  `subject` text NOT NULL,
  `description` text NOT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document`
--

INSERT INTO `document` (`id`, `document_type`, `document_id`, `title`, `authored_by`, `date`, `subject`, `description`, `is_archived`, `file_path`) VALUES
(9, 'Resolution', 'Resolution-00001-15-01-2025', 'sample reso', 3, '2025-01-14', 'sample reso', 'sample reso', 0, '../uploads/678723e71d049.pdf'),
(10, 'Resolution', 'Resolution-00002-15-01-2025', 'sample reso', 3, '2025-01-13', 'sample reso', 'sample reso', 0, '../uploads/6787245d57b80.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `documentfiles`
--

CREATE TABLE `documentfiles` (
  `id` int(11) NOT NULL,
  `document_id` varchar(100) NOT NULL,
  `file_type` enum('pdf','docx','jpg','png','xlsx') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `uploaded_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documentfiles`
--

INSERT INTO `documentfiles` (`id`, `document_id`, `file_type`, `file_path`, `uploaded_at`, `uploaded_by`) VALUES
(8, 'Resolution-00001-15-01-2025', 'pdf', '../uploads/678723e71d049.pdf', '2025-01-15 10:56:39', 3),
(9, 'Resolution-00002-15-01-2025', 'pdf', '../uploads/6787245d57b80.pdf', '2025-01-15 10:58:37', 3);

-- --------------------------------------------------------

--
-- Table structure for table `documenttimeline`
--

CREATE TABLE `documenttimeline` (
  `id` int(11) NOT NULL,
  `document_id` varchar(100) NOT NULL,
  `status` enum('Pending','First Reading','Second Reading','Third Reading','Committee Review','For Approval','Approved','Rejected') NOT NULL,
  `status_date` datetime DEFAULT current_timestamp(),
  `action_by` int(11) NOT NULL,
  `action_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documenttimeline`
--

INSERT INTO `documenttimeline` (`id`, `document_id`, `status`, `status_date`, `action_by`, `action_reason`) VALUES
(2, 'Resolution-00001-15-01-2025', 'Pending', '2025-01-15 10:56:39', 3, 'Document is pending approval.'),
(3, 'Resolution-00002-15-01-2025', 'Pending', '2025-01-15 10:58:37', 3, 'Document is pending approval.');

-- --------------------------------------------------------

--
-- Table structure for table `documenttracking`
--

CREATE TABLE `documenttracking` (
  `id` int(11) NOT NULL,
  `document_id` varchar(100) NOT NULL,
  `related_event_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emailverification`
--

CREATE TABLE `emailverification` (
  `id` int(11) NOT NULL,
  `user_type_id` int(11) NOT NULL,
  `verification_code` varchar(255) NOT NULL,
  `expiry_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emailverification`
--

INSERT INTO `emailverification` (`id`, `user_type_id`, `verification_code`, `expiry_date`) VALUES
(1, 1, 'abc123', '2025-02-13 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `employee_details`
--

CREATE TABLE `employee_details` (
  `employee_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `office_location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_details`
--

INSERT INTO `employee_details` (`employee_id`, `department_id`, `designation`, `hire_date`, `office_location`) VALUES
(1, 1, 'Chief Financial Officer', '2020-01-15', 'Finance Office');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `event_type` enum('Cultural','Special Session','Governmental') NOT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `archival_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `name`, `date`, `approved_by`, `event_type`, `is_archived`, `archival_reason`) VALUES
(1, 'New Event', '2025-01-15', 1, 'Cultural', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lgudetails`
--

CREATE TABLE `lgudetails` (
  `id` int(11) NOT NULL,
  `history` text NOT NULL,
  `hotline` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `mission` text NOT NULL,
  `vision` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lgudetails`
--

INSERT INTO `lgudetails` (`id`, `history`, `hotline`, `email`, `address`, `mission`, `vision`, `created_at`) VALUES
(1, 'LGU History', '123-4567', 'lgu@example.com', '123 LGU St.', 'Serve the people', 'Be the best', '2025-01-13 12:58:33');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_type_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `notification_type` enum('Download','StatusUpdate') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `user_type_id`, `message`, `notification_type`, `is_read`, `created_at`) VALUES
(1, 1, 'Document approved.', 'StatusUpdate', 0, '2025-01-13 12:58:33');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_name`, `description`) VALUES
(1, 'View Documents', 'Allows the user to view documents in the system'),
(2, 'Edit Documents', 'Allows the user to edit existing documents'),
(3, 'Approve Documents', 'Grants the user the ability to approve documents'),
(4, 'Create Users', 'Allows the user to create new user accounts'),
(5, 'Delete Documents', 'Allows the user to delete documents from the system');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `position_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `position_name`) VALUES
(1, 'Manager');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Admin', 'Administrator role for managing system operations'),
(2, 'Employee', 'Administrator role for managing system operations');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `first_name`, `last_name`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin@example.com', 'Admin', 'User', 1, 'active', '2025-01-13 04:58:33', '2025-01-13 04:58:33'),
(2, 'patrick', 'fe4c07d68058d897823e82c9d3ed99254027badb8bb12710668c6c041eae5e29240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'patrickdulin02@gmail.com', 'John Patrick', 'Dulin', 2, 'active', '2025-01-13 05:07:16', '2025-01-13 05:24:33'),
(3, 'winny', '461d4815bd4312a12ba9bc72b1ba3a45d8e2f8fa4dcf22284c591354f8f497cc', 'paul@gmail.com', 'Winny', 'Ramos', 2, 'active', '2025-01-13 05:31:37', '2025-01-14 19:35:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminlogs`
--
ALTER TABLE `adminlogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `archiveddocuments`
--
ALTER TABLE `archiveddocuments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_id` (`document_id`),
  ADD KEY `authored_by` (`authored_by`);

--
-- Indexes for table `carouselcontent`
--
ALTER TABLE `carouselcontent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_id` (`document_id`),
  ADD KEY `authored_by` (`authored_by`);

--
-- Indexes for table `documentfiles`
--
ALTER TABLE `documentfiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `documenttimeline`
--
ALTER TABLE `documenttimeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `action_by` (`action_by`);

--
-- Indexes for table `documenttracking`
--
ALTER TABLE `documenttracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `related_event_id` (`related_event_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `emailverification`
--
ALTER TABLE `emailverification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type_id` (`user_type_id`);

--
-- Indexes for table `employee_details`
--
ALTER TABLE `employee_details`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `lgudetails`
--
ALTER TABLE `lgudetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type_id` (`user_type_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `position_name` (`position_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminlogs`
--
ALTER TABLE `adminlogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `archiveddocuments`
--
ALTER TABLE `archiveddocuments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carouselcontent`
--
ALTER TABLE `carouselcontent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `documentfiles`
--
ALTER TABLE `documentfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `documenttimeline`
--
ALTER TABLE `documenttimeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `documenttracking`
--
ALTER TABLE `documenttracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `emailverification`
--
ALTER TABLE `emailverification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lgudetails`
--
ALTER TABLE `lgudetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adminlogs`
--
ALTER TABLE `adminlogs`
  ADD CONSTRAINT `adminlogs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `archiveddocuments`
--
ALTER TABLE `archiveddocuments`
  ADD CONSTRAINT `archiveddocuments_ibfk_1` FOREIGN KEY (`authored_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `document`
--
ALTER TABLE `document`
  ADD CONSTRAINT `document_ibfk_1` FOREIGN KEY (`authored_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `documentfiles`
--
ALTER TABLE `documentfiles`
  ADD CONSTRAINT `documentfiles_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `document` (`document_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documentfiles_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `documenttimeline`
--
ALTER TABLE `documenttimeline`
  ADD CONSTRAINT `documenttimeline_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `document` (`document_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documenttimeline_ibfk_2` FOREIGN KEY (`action_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `documenttracking`
--
ALTER TABLE `documenttracking`
  ADD CONSTRAINT `documenttracking_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `document` (`document_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documenttracking_ibfk_2` FOREIGN KEY (`related_event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documenttracking_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documenttracking_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `emailverification`
--
ALTER TABLE `emailverification`
  ADD CONSTRAINT `emailverification_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_details`
--
ALTER TABLE `employee_details`
  ADD CONSTRAINT `employee_details_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `employee_details_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
