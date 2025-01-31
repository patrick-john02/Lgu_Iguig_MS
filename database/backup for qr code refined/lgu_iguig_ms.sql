-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2025 at 12:00 PM
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
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `break_out` time DEFAULT NULL,
  `break_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` enum('Present','Absent','Late') DEFAULT 'Present',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `user_id`, `date`, `check_in`, `break_out`, `break_in`, `check_out`, `status`, `remarks`, `created_at`) VALUES
(1, 2, '2025-01-21', '08:05:00', '12:00:00', '12:30:00', '17:45:00', 'Late', 'Late check-in; ', '2025-01-21 03:50:55'),
(2, 26, '2025-01-21', '08:00:00', '12:15:00', '12:45:00', '17:00:00', 'Absent', '', '2025-01-21 03:50:55');

--
-- Triggers `attendance`
--
DELIMITER $$
CREATE TRIGGER `validate_and_update_attendance` BEFORE INSERT ON `attendance` FOR EACH ROW BEGIN
  -- Default remarks
  SET NEW.remarks = '';

  -- Determine if the employee is late
  IF NEW.check_in > '08:00:00' THEN
    SET NEW.status = 'Late';
    SET NEW.remarks = CONCAT(NEW.remarks, 'Late check-in; ');
  END IF;

  -- Calculate break duration
  IF NEW.break_out IS NOT NULL AND NEW.break_in IS NOT NULL THEN
    IF TIMEDIFF(NEW.break_in, NEW.break_out) > '01:00:00' THEN
      SET NEW.remarks = CONCAT(NEW.remarks, 'Undertime during break; ');
    END IF;
  END IF;

  -- Determine overtime (if check-out is 6:00 PM or later)
  IF NEW.check_out IS NOT NULL AND NEW.check_out >= '18:00:00' THEN
    SET NEW.remarks = CONCAT(NEW.remarks, 'Overtime; ');
    IF NEW.status <> 'Late' THEN
      SET NEW.status = 'Present';
    END IF; -- Keep 'Late' status if already late
  END IF;

  -- Check for undertime (if check-out is before 5:00 PM)
  IF NEW.check_out IS NOT NULL AND NEW.check_out < '17:00:00' THEN
    SET NEW.remarks = CONCAT(NEW.remarks, 'Left early (undertime); ');
  END IF;

  -- Ensure proper status for Present/Absent
  IF NEW.check_in IS NULL OR NEW.check_out IS NULL THEN
    SET NEW.status = 'Absent';
    SET NEW.remarks = CONCAT(NEW.remarks, 'Incomplete attendance; ');
  ELSE
    IF NEW.status IS NULL THEN
      SET NEW.status = 'Present';
    END IF;
  END IF;

  -- Log errors if attendance is incomplete
  IF NEW.status = 'Absent' THEN
    INSERT INTO `attendance_logs` (`user_id`, `error_message`) 
    VALUES (NEW.user_id, 'Incomplete attendance data (missing check-in/check-out)');
  END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attendance_id` int(11) DEFAULT NULL,
  `error_message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`log_id`, `user_id`, `attendance_id`, `error_message`, `created_at`) VALUES
(1, 1, 1, 'Incomplete attendance data (missing check-in/check-out)', '2025-01-21 04:13:19'),
(2, 2, 2, 'No errors, attendance data complete', '2025-01-21 04:13:19');

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
  `is_approved` tinyint(1) DEFAULT 0,
  `is_rejected` tinyint(1) DEFAULT 0,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document`
--

INSERT INTO `document` (`id`, `document_type`, `document_id`, `title`, `authored_by`, `date`, `subject`, `description`, `is_archived`, `is_approved`, `is_rejected`, `file_path`) VALUES
(9, 'Resolution', 'Resolution-00001-15-01-2025', 'Resolution for Free Public Wi-Fi Initiative', 3, '2025-01-14', 'Free Public Wi-Fi Access', 'This resolution proposes the establishment of free public Wi-Fi hotspots in key areas of the city to support digital inclusion.', 0, 0, 1, '../uploads/678723e71d049.pdf'),
(10, 'Resolution', 'Resolution-00002-15-01-2025', 'Resolution for Urban Green Spaces', 3, '2025-01-13', 'Development of Urban Green Spaces', 'This resolution proposes the creation and maintenance of green spaces in urban areas to improve air quality and public health.', 0, 1, 0, '../uploads/6787245d57b80.pdf'),
(11, 'Resolution', 'Resolution-00003-18-01-2025', 'Resolution for Renewable Energy Investments', 3, '2025-01-18', 'Investments in Renewable Energy Projects', 'This resolution advocates for increased investments in renewable energy projects, including solar and wind energy developments, to reduce carbon emissions.', 0, 1, 0, '../uploads/678b2bbe63538.pdf'),
(12, 'Memorandum', 'Memorandum-00001-20-01-2025', 'Memorandum on Scheduled System Maintenance', 1, '2025-01-20', 'System Maintenance Notification', 'This memorandum notifies staff of the upcoming system maintenance scheduled for the weekend, during which access to certain internal tools will be unavailable.', 0, 0, 0, '../uploads/678d875314c6f.pdf'),
(13, 'Memorandum', 'Memorandum-00002-20-01-2025', 'Memorandum on New Security Procedures for Building Access', 1, '2025-01-20', 'New Security Procedures', 'This memorandum informs employees about the new building access security procedures, which include ID card scanning at all entry points.', 0, 0, 0, '../uploads/678d877d90b64.pdf'),
(14, 'Memorandum', 'Memorandum-00003-20-01-2025', 'Memorandum on Employee Attendance Policy Update', 1, '2025-01-20', 'Update on Attendance Policy', 'This memorandum communicates the updated policies on employee attendance and punctuality, effective from the start of the next quarter.', 0, 0, 0, '../uploads/678d8de1813a8.pdf'),
(20, 'Memorandum', 'Memorandum-00004-20-01-2025', 'll', 1, '2025-01-21', 'll', 'lll', 0, 0, 0, '../uploads/678e920306908.pdf'),
(21, 'Memorandum', 'Memorandum-00005-20-01-2025', 'lll', 1, '2025-01-21', 'lll', 'lll', 0, 0, 0, '../uploads/678e935523063.pdf'),
(22, 'Memorandum', 'Memorandum-00006-20-01-2025', 'hhh', 1, '2025-01-21', 'hhh', 'hhh', 0, 0, 0, '../uploads/678e9392ef2a4.pdf');

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
(19, 'Memorandum-00004-20-01-2025', 'pdf', '../uploads/678e920306908.pdf', '2025-01-21 02:12:19', 1),
(20, 'Memorandum-00005-20-01-2025', 'pdf', '../uploads/678e935523063.pdf', '2025-01-21 02:17:57', 1),
(21, 'Memorandum-00006-20-01-2025', 'pdf', '../uploads/678e9392ef2a4.pdf', '2025-01-21 02:18:58', 1);

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
(29, 'Memorandum-00004-20-01-2025', 'Pending', '2025-01-21 02:12:19', 1, 'Document is pending approval.'),
(30, 'Memorandum-00005-20-01-2025', 'Pending', '2025-01-21 02:17:57', 1, 'Document is pending approval.'),
(31, 'Memorandum-00006-20-01-2025', 'Pending', '2025-01-21 02:18:58', 1, 'Document is pending approval.');

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
  `name` text NOT NULL,
  `date` date NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `event_type` enum('Cultural','Special Session','Governmental') NOT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `archival_reason` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `name`, `date`, `approved_by`, `event_type`, `is_archived`, `archival_reason`, `image_path`) VALUES
(1, 'WE ARE HIRING\nLIAISON OFFICER\n', '2025-01-15', 1, 'Governmental', 0, NULL, '/uploads/events/hiring1.jpg'),
(2, 'WE ARE HIRING\nLIAISON OFFICER\n', '2025-01-15', 1, 'Governmental', 0, NULL, '/uploads/events/hiring.jpg'),
(11, 'Congratulations Municipality of Iguig, under the leadership of Municipal Mayor, Hon. Ferdinand B. Trinidad. 🎉👊', '2025-01-18', 1, 'Governmental', 0, NULL, '/uploads/events/child.jpg'),
(12, 'Bukas parin po ang aplikasyon para sa \"Libreng Kasalan ng Bayan\" hanggang sa January 31, 2025. Matungo lamang po sa opisina ng Municipal Civil Registrar para sa kinakailangang dokumento at iba pang mga katanungan. \nMaraming salamat po.', '2025-01-18', 1, 'Special Session', 0, NULL, '/uploads/events/kasal.jpg'),
(14, 'testiung', '0000-00-00', 1, 'Governmental', 0, NULL, '../uploads/events/images.png'),
(18, 'To all Candidates (National and Local) & Party List Group...\nList of Designated Common Poster Areas where you can post your election campaign materials in connection with the May 12, 2025 National and Local Elections pursuant to Sec. 21 (e) of Comelec Resolution No. 11086 in re: R.A. 9006 otherwise known as the “Fair Election Act”', '2025-01-20', 1, 'Special Session', 0, NULL, '/uploads/events/toall.jpg');

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
(3, 'LNB President'),
(1, 'Manager'),
(5, 'Municipal Vice Mayor'),
(4, 'Sangguniang  Bayan Member'),
(2, 'Sangguniang Bayan Member'),
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
(3, 'Super Admin', 'Highest-level role with access to all system operations and configurations');

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
  `profile_picture` varchar(255) DEFAULT '../profile/default.jpg',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role_id` int(11) NOT NULL,
  `position_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `profile_picture`, `first_name`, `last_name`, `role_id`, `position_id`, `status`, `created_at`, `updated_at`, `is_deleted`) VALUES
(1, 'admin', '$2y$10$QyeZBiMYXn3arJNLY440g.wXEEPGt.Y9czXlkEeK42bxRo1aKeFQu', 'lguiguigadmin@gmail.com', 'profile/astig.jpg', 'Lgu Iguig', 'Admin', 1, 3, 'active', '2025-01-13 04:58:33', '2025-01-21 03:11:25', 0),
(2, 'angelita', '$2y$10$SoZgNKZJHSdf0mDV4TRuMeIFzfY4uRMg55Poa6U6iLjnpBXeMu7LC', 'angelbanatao19@gmail.com', '/profile/default.jpg', 'Angelita', 'Banatao', 3, 5, 'active', '2025-01-13 05:07:16', '2025-01-21 03:11:28', 0),
(3, 'winny', '$2y$10$v3bmi8wB0GsucFaTtc53HugzC4kOT1lRejRMpPw0KMVAzKCmQn7ua', 'winnybalanzaramos@gmail.com', './profile/winny.jpg', 'Winny', 'Ramos', 2, 6, 'active', '2025-01-13 05:31:37', '2025-01-20 00:18:19', 0),
(20, 'jonalyn.tammidao', '$2y$10$MIA9lXzUMMNLo94Tv17kneurQ7k/UVUHCusbTyM4ZaRK7HSMWIY56', 'joantamm@gmail.com', './profile/jonalyn.jpg', 'Jonalyn', 'Tammidao', 2, 3, 'inactive', '2025-01-17 02:36:16', '2025-01-19 23:57:35', 0),
(26, 'patick.dulin', '$2y$10$1deE3LkYqjV6uAy5sI4L9e63uBgO73Bz.jHKeMyBx5SiGvf2D8Eo.', 'patrickdulin02@gmail.com', '../profile/default.jpg', 'patick', 'dulin', 2, 1, 'active', '2025-01-20 00:20:53', '2025-01-21 06:00:46', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_qr_codes`
--

CREATE TABLE `user_qr_codes` (
  `qr_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `qr_code_image` varchar(255) DEFAULT NULL,
  `qr_code_data` text NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sent_to_email` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `user_id_date` (`user_id`,`date`),
  ADD UNIQUE KEY `unique_user_date` (`user_id`,`date`),
  ADD KEY `idx_user_date` (`user_id`,`date`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_attendance_logs` (`attendance_id`);

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
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_event_date` (`date`);

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
  ADD KEY `role_id` (`role_id`),
  ADD KEY `fk_users_positions` (`position_id`),
  ADD KEY `idx_users_role` (`role_id`);

--
-- Indexes for table `user_qr_codes`
--
ALTER TABLE `user_qr_codes`
  ADD PRIMARY KEY (`qr_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `documentfiles`
--
ALTER TABLE `documentfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `documenttimeline`
--
ALTER TABLE `documenttimeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `user_qr_codes`
--
ALTER TABLE `user_qr_codes`
  MODIFY `qr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_logs_ibfk_2` FOREIGN KEY (`attendance_id`) REFERENCES `attendance` (`attendance_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attendance_logs` FOREIGN KEY (`attendance_id`) REFERENCES `attendance` (`attendance_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_event_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `fk_users_positions` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `user_qr_codes`
--
ALTER TABLE `user_qr_codes`
  ADD CONSTRAINT `user_qr_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
