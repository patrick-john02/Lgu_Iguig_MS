-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2025 at 04:44 PM
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
  `is_approved` tinyint(1) DEFAULT 0,
  `is_rejected` tinyint(1) DEFAULT 0,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document`
--

INSERT INTO `document` (`id`, `document_type`, `document_id`, `title`, `authored_by`, `date`, `subject`, `description`, `is_archived`, `is_approved`, `is_rejected`, `file_path`) VALUES
(9, 'Resolution', 'Resolution-00001-15-01-2025', 'Resolution for Free Public Wi-Fi Initiative', 3, '2025-01-14', 'Free Public Wi-Fi Access', 'This resolution proposes the establishment of free public Wi-Fi hotspots in key areas of the city to support digital inclusion.', 0, 0, 1, '../uploads/678723e71d049.pdf'),
(10, 'Resolution', 'Resolution-00002-15-01-2025', 'Resolution for Urban Green Spaces', 3, '2025-01-13', 'Development of Urban Green Spaces', 'This resolution proposes the creation and maintenance of green spaces in urban areas to improve air quality and public health.', 0, 0, 0, '../uploads/6787245d57b80.pdf'),
(11, 'Resolution', 'Resolution-00003-18-01-2025', 'Resolution for Renewable Energy Investments', 3, '2025-01-18', 'Investments in Renewable Energy Projects', 'This resolution advocates for increased investments in renewable energy projects, including solar and wind energy developments, to reduce carbon emissions.', 0, 1, 0, '../uploads/678b2bbe63538.pdf'),
(12, 'Memorandum', 'Memorandum-00001-20-01-2025', 'Memorandum on Scheduled System Maintenance', 1, '2025-01-20', 'System Maintenance Notification', 'This memorandum notifies staff of the upcoming system maintenance scheduled for the weekend, during which access to certain internal tools will be unavailable.', 0, 0, 0, '../uploads/678d875314c6f.pdf'),
(13, 'Memorandum', 'Memorandum-00002-20-01-2025', 'Memorandum on New Security Procedures for Building Access', 1, '2025-01-20', 'New Security Procedures', 'This memorandum informs employees about the new building access security procedures, which include ID card scanning at all entry points.', 0, 0, 0, '../uploads/678d877d90b64.pdf'),
(14, 'Memorandum', 'Memorandum-00003-20-01-2025', 'Memorandum on Employee Attendance Policy Update', 1, '2025-01-20', 'Update on Attendance Policy', 'This memorandum communicates the updated policies on employee attendance and punctuality, effective from the start of the next quarter.', 0, 0, 0, '../uploads/678d8de1813a8.pdf'),
(15, 'Resolution', 'Resolution-00004-26-01-2025', 'saa', 26, '2025-01-26', 'saa', 'sdasa', 0, 0, 0, '../uploads/6795a402cf863.pdf');

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
(9, 'Resolution-00002-15-01-2025', 'pdf', '../uploads/6787245d57b80.pdf', '2025-01-15 10:58:37', 3),
(10, 'Resolution-00003-18-01-2025', 'pdf', '../uploads/678b2bbe63538.pdf', '2025-01-18 12:19:10', 3),
(11, 'Memorandum-00001-20-01-2025', 'pdf', '../uploads/678d875314c6f.pdf', '2025-01-20 07:14:27', 1),
(12, 'Memorandum-00002-20-01-2025', 'pdf', '../uploads/678d877d90b64.pdf', '2025-01-20 07:15:09', 1),
(13, 'Memorandum-00003-20-01-2025', 'pdf', '../uploads/678d8de1813a8.pdf', '2025-01-20 07:42:25', 1),
(14, 'Resolution-00004-26-01-2025', 'pdf', '../uploads/6795a402cf863.pdf', '2025-01-26 10:54:58', 26);

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
(3, 'Resolution-00002-15-01-2025', 'Pending', '2025-01-15 10:58:37', 3, 'Document is pending approval.'),
(4, 'Resolution-00002-15-01-2025', 'First Reading', '2025-01-16 12:12:24', 1, NULL),
(5, 'Resolution-00002-15-01-2025', 'Second Reading', '2025-01-16 12:23:51', 1, 'meeting'),
(6, 'Resolution-00002-15-01-2025', 'Third Reading', '2025-01-16 12:24:06', 1, 'meeting'),
(7, 'Resolution-00002-15-01-2025', 'Committee Review', '2025-01-16 12:24:51', 1, 'meeting'),
(8, 'Resolution-00002-15-01-2025', 'For Approval', '2025-01-16 12:24:56', 1, 'meeting'),
(9, 'Resolution-00002-15-01-2025', 'Approved', '2025-01-16 12:25:07', 1, 'complete document. '),
(10, 'Resolution-00001-15-01-2025', 'First Reading', '2025-01-17 14:12:48', 1, 'use our own format'),
(11, 'Resolution-00001-15-01-2025', 'Second Reading', '2025-01-17 14:15:07', 1, 'ongoing'),
(12, 'Resolution-00001-15-01-2025', 'Third Reading', '2025-01-17 14:27:48', 1, 'ongoing'),
(13, 'Resolution-00001-15-01-2025', 'Rejected', '2025-01-17 14:33:34', 1, 'incomplete document'),
(14, 'Resolution-00003-18-01-2025', 'Pending', '2025-01-18 12:19:10', 3, 'Document is pending approval.'),
(15, 'Resolution-00003-18-01-2025', 'First Reading', '2025-01-18 12:20:12', 1, 'ongoing'),
(16, 'Resolution-00003-18-01-2025', 'Second Reading', '2025-01-18 12:41:37', 1, 'wait'),
(17, 'Resolution-00003-18-01-2025', 'Third Reading', '2025-01-19 03:46:48', 1, 'ongoing'),
(18, 'Resolution-00003-18-01-2025', 'Committee Review', '2025-01-19 03:47:38', 1, 'ongoing'),
(19, 'Resolution-00003-18-01-2025', 'For Approval', '2025-01-19 03:48:49', 1, 'waiting for approval of the panel'),
(20, 'Resolution-00003-18-01-2025', 'Approved', '2025-01-19 03:49:24', 1, 'wait '),
(21, 'Memorandum-00001-20-01-2025', 'Pending', '2025-01-20 07:14:27', 1, 'Document is pending approval.'),
(22, 'Memorandum-00002-20-01-2025', 'Pending', '2025-01-20 07:15:09', 1, 'Document is pending approval.'),
(23, 'Memorandum-00003-20-01-2025', 'Pending', '2025-01-20 07:42:25', 1, 'Document is pending approval.'),
(24, 'Resolution-00004-26-01-2025', 'Pending', '2025-01-26 10:54:58', 26, 'Document is pending approval.'),
(25, 'Resolution-00004-26-01-2025', 'First Reading', '2025-01-26 10:55:16', 26, 'sa');

--
-- Triggers `documenttimeline`
--
DELIMITER $$
CREATE TRIGGER `update_document_status` AFTER INSERT ON `documenttimeline` FOR EACH ROW BEGIN
    -- Check if the new status is Approved
    IF NEW.status = 'Approved' THEN
        UPDATE `document` 
        SET is_approved = 1, is_rejected = 0 
        WHERE document_id = NEW.document_id;
    END IF;

    -- Check if the new status is Rejected
    IF NEW.status = 'Rejected' THEN
        UPDATE `document` 
        SET is_rejected = 1, is_approved = 0 
        WHERE document_id = NEW.document_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `date` date NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `event_type` enum('Cultural','Special Session','Governmental') NOT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `name`, `date`, `approved_by`, `event_type`, `is_archived`, `image_path`) VALUES
(1, 'WE ARE HIRING\nLIAISON OFFICER\n', '2025-01-15', 1, 'Governmental', 0, '/uploads/events/hiring1.jpg'),
(2, 'WE ARE HIRING\nLIAISON OFFICER\n', '2025-01-15', 1, 'Governmental', 0, '/uploads/events/hiring.jpg'),
(11, 'Congratulations Municipality of Iguig, under the leadership of Municipal Mayor, Hon. Ferdinand B. Trinidad. 🎉👊', '2025-01-18', 1, 'Governmental', 0, '/uploads/events/child.jpg'),
(12, 'Bukas parin po ang aplikasyon para sa \"Libreng Kasalan ng Bayan\" hanggang sa January 31, 2025. Matungo lamang po sa opisina ng Municipal Civil Registrar para sa kinakailangang dokumento at iba pang mga katanungan. \nMaraming salamat po.', '2025-01-18', 1, 'Special Session', 0, '/uploads/events/kasal.jpg'),
(14, 'testiung', '0000-00-00', 1, 'Governmental', 0, '../uploads/events/images.png'),
(18, 'To all Candidates (National and Local) & Party List Group...\nList of Designated Common Poster Areas where you can post your election campaign materials in connection with the May 12, 2025 National and Local Elections pursuant to Sec. 21 (e) of Comelec Resolution No. 11086 in re: R.A. 9006 otherwise known as the “Fair Election Act”', '2025-01-20', 1, 'Special Session', 0, '/uploads/events/toall.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `landisputes`
--

CREATE TABLE `landisputes` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `landisputes`
--

INSERT INTO `landisputes` (`id`, `title`, `description`, `date`) VALUES
(1, 'aaa', 'aaaa', '2025-01-27'),
(2, 'sss', 'sss', '2025-01-27');

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
(3, 'LNB President'),
(1, 'Manager'),
(5, 'Municipal Vice Mayor'),
(4, 'Sangguniang  Bayan Member'),
(2, 'Sangguniang Bayan Member'),
(7, 'Secretary'),
(6, 'SK Federation President');

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
(2, 'Employee', 'Administrator role for managing system operations'),
(3, 'SuperAdmin', 'Access All Features');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Name or purpose of the session',
  `subject` varchar(255) NOT NULL COMMENT 'Subject or agenda of the session',
  `event_type` enum('Meeting','Session') NOT NULL COMMENT 'Type of event (Meeting or Session)',
  `host_id` int(11) DEFAULT NULL COMMENT 'Host or creator of the session',
  `start_time` time DEFAULT '13:00:00' COMMENT 'Start time of the session (For daily sessions)',
  `recurrence` enum('Daily','Weekly','Monthly','One-time') DEFAULT 'One-time' COMMENT 'Recurrence of session (applicable only for sessions)',
  `date` datetime DEFAULT NULL COMMENT 'Specific date and time for meetings, NULL for sessions',
  `description` text DEFAULT NULL COMMENT 'Optional description or details about the session',
  `created_by` int(11) NOT NULL COMMENT 'User who created the session',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Timestamp when the session was created'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `name`, `subject`, `event_type`, `host_id`, `start_time`, `recurrence`, `date`, `description`, `created_by`, `created_at`) VALUES
(1, 'dsa', 'dsa', 'Meeting', 1, '13:00:00', 'One-time', '2025-01-28 12:50:00', 'dsa', 1, '2025-01-28 07:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `session_attendance`
--

CREATE TABLE `session_attendance` (
  `attendance_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL COMMENT 'Reference to the session',
  `user_id` int(11) NOT NULL COMMENT 'Reference to the user',
  `status` enum('Present','Absent','Late') DEFAULT 'Present' COMMENT 'Attendance status',
  `time_recorded` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Time when attendance was recorded'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session_attendance`
--

INSERT INTO `session_attendance` (`attendance_id`, `session_id`, `user_id`, `status`, `time_recorded`) VALUES
(1, 1, 1, 'Present', '2025-01-27 06:37:27'),
(2, 1, 3, 'Present', '2025-01-28 02:45:36');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_attendance`
--

CREATE TABLE `tbl_attendance` (
  `tbl_attendance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time_in` timestamp NOT NULL DEFAULT current_timestamp(),
  `break_out` timestamp NULL DEFAULT NULL,
  `break_in` timestamp NULL DEFAULT NULL,
  `time_out` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_attendance`
--

INSERT INTO `tbl_attendance` (`tbl_attendance_id`, `user_id`, `time_in`, `break_out`, `break_in`, `time_out`, `created_at`, `updated_at`) VALUES
(1, 20, '2025-01-22 06:49:05', '2025-01-22 06:49:01', '2025-01-22 06:49:17', '2025-01-22 15:31:03', '2025-01-22 12:41:48', '2025-01-30 15:31:05'),
(2, 26, '2025-01-24 15:51:10', '2025-01-22 07:38:07', '2025-01-22 07:20:04', '2025-01-22 09:49:42', '2025-01-22 13:42:32', '2025-01-24 15:51:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT '../profile/default.jpg',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role_id` int(11) NOT NULL,
  `position_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  `generated_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `profile_picture`, `first_name`, `last_name`, `role_id`, `position_id`, `status`, `created_at`, `updated_at`, `is_deleted`, `generated_code`) VALUES
(1, 'admin', '$2y$10$QyeZBiMYXn3arJNLY440g.wXEEPGt.Y9czXlkEeK42bxRo1aKeFQu', 'lguiguigadmin@gmail.com', 'profile/astig.jpg', 'Lgu Iguig', 'Admin', 1, 5, 'active', '2025-01-13 04:58:33', '2025-01-20 00:17:32', 0, ''),
(2, 'angelita', '$2y$10$SoZgNKZJHSdf0mDV4TRuMeIFzfY4uRMg55Poa6U6iLjnpBXeMu7LC', 'angelbanatao19@gmail.com', '/profile/default.jpg', 'Angelita', 'Banatao', 2, 3, 'active', '2025-01-13 05:07:16', '2025-01-20 00:17:46', 0, ''),
(3, 'winny', '$2y$10$v3bmi8wB0GsucFaTtc53HugzC4kOT1lRejRMpPw0KMVAzKCmQn7ua', 'winnybalanzaramos@gmail.com', './profile/winny.jpg', 'Winny', 'Ramos', 2, 6, 'active', '2025-01-13 05:31:37', '2025-01-24 12:21:42', 0, 'code_679385d62a824'),
(20, 'jonalyn.tammidao', '$2y$10$MIA9lXzUMMNLo94Tv17kneurQ7k/UVUHCusbTyM4ZaRK7HSMWIY56', 'joantamm@gmail.com', './profile/jonalyn.jpg', 'Jonalyn', 'Tammidao', 2, 3, 'active', '2025-01-17 02:36:16', '2025-01-24 12:20:55', 0, 'code_679385a70f4c8'),
(26, 'patrick.dulin', '$2y$10$QyeZBiMYXn3arJNLY440g.wXEEPGt.Y9czXlkEeK42bxRo1aKeFQu', 'patrickdulin02@gmail.com', '../profile/default.jpg', 'patick', 'dulin', 3, 4, 'active', '2025-01-20 00:20:53', '2025-01-24 15:54:02', 0, 'code_6790e76012f95'),
(27, 'carl.supan', '$2y$10$b9hZ7a38l1T36pIKI3iUMO7XXmkDR8aT9HVjniyH4EwXn3445Z7LW', 'carlcastuel@gmail.com', '../profile/default.jpg', 'carl', 'supan', 2, 4, 'active', '2025-01-24 16:08:39', '2025-01-24 16:09:15', 0, 'code_6793bb2becb21');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_event_date` (`date`);

--
-- Indexes for table `landisputes`
--
ALTER TABLE `landisputes`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `host_id` (`host_id`);

--
-- Indexes for table `session_attendance`
--
ALTER TABLE `session_attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_attendance`
--
ALTER TABLE `tbl_attendance`
  ADD PRIMARY KEY (`tbl_attendance_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `fk_users_positions` (`position_id`),
  ADD KEY `idx_users_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `documentfiles`
--
ALTER TABLE `documentfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `documenttimeline`
--
ALTER TABLE `documenttimeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `landisputes`
--
ALTER TABLE `landisputes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `session_attendance`
--
ALTER TABLE `session_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_attendance`
--
ALTER TABLE `tbl_attendance`
  MODIFY `tbl_attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

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
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_event_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `session_attendance`
--
ALTER TABLE `session_attendance`
  ADD CONSTRAINT `session_attendance_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`session_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `session_attendance_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_attendance`
--
ALTER TABLE `tbl_attendance`
  ADD CONSTRAINT `tbl_attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_positions` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
