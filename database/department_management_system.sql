-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 24, 2026 at 03:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `department_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_sessions`
--

CREATE TABLE `academic_sessions` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `starts_on` date NOT NULL,
  `ends_on` date NOT NULL,
  `is_current` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_sessions`
--

INSERT INTO `academic_sessions` (`id`, `name`, `starts_on`, `ends_on`, `is_current`) VALUES
(1, '2025/2026', '2025-09-01', '2026-08-31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(160) NOT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `body` text NOT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `body`, `status`, `published_at`, `created_by`, `created_at`) VALUES
(1, 'Registration is open', 'Students and staff can now complete departmental registration through the DMS portal.', 'published', '2026-07-08 18:30:55', 1, '2026-07-08 18:30:55'),
(2, 'Timetable draft published', 'The first semester timetable is available for review.', 'published', '2026-07-08 18:30:55', 1, '2026-07-08 18:30:55'),
(3, 'Upcoming General Meeting', 'There will be a general meeting on Monday 2nd August 2026', 'published', '2026-07-09 00:19:35', 1, '2026-07-08 23:19:35'),
(4, 'New Upcoming Departmental Meeting', 'Discussion of uprising olodo students', 'published', '2026-07-19 19:58:49', 1, '2026-07-19 18:58:49');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_roles`
--

CREATE TABLE `announcement_roles` (
  `announcement_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_roles`
--

INSERT INTO `announcement_roles` (`announcement_id`, `role_id`) VALUES
(3, 1),
(3, 2),
(3, 3),
(4, 1),
(4, 2),
(4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_name` varchar(120) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `table_name`, `record_id`, `old_values`, `new_values`, `created_at`) VALUES
(1, 1, 'timetable', 2, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Friday\"}', '2026-07-09 03:19:35'),
(2, 1, 'timetable', 2, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Saturday\"}', '2026-07-09 03:41:01'),
(3, 1, 'timetable', 2, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Friday\"}', '2026-07-09 03:41:03'),
(4, 1, 'timetable', 2, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Saturday\"}', '2026-07-09 03:41:05'),
(5, 1, 'timetable', 2, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Friday\"}', '2026-07-09 03:41:08'),
(6, 1, 'timetable', 2, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Saturday\"}', '2026-07-09 03:41:14'),
(7, 1, 'timetable', 2, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Friday\"}', '2026-07-09 03:41:19'),
(8, 1, 'timetable', 2, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Saturday\"}', '2026-07-09 03:41:45'),
(9, 1, 'timetable', 2, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Friday\"}', '2026-07-09 03:43:39'),
(10, 1, 'timetable', 1, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Tuesday\"}', '2026-07-09 11:57:34'),
(11, 1, 'timetable', 1, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Monday\"}', '2026-07-09 11:57:35'),
(12, 1, 'timetable', 3, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Thursday\"}', '2026-07-09 11:57:45'),
(13, 1, 'timetable', 3, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Wednesday\"}', '2026-07-09 11:57:53'),
(14, 1, 'courses', 7, NULL, '{\"code\":\"COM 111\",\"title\":\"Introduction to Programming\"}', '2026-07-19 18:46:15'),
(15, 1, 'timetable', 4, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Thursday\"}', '2026-07-19 18:48:38'),
(16, 1, 'timetable', 4, NULL, '{\"action\":\"drag_update\",\"new_day\":\"Wednesday\"}', '2026-07-19 18:48:42'),
(17, 1, 'expenses', 2, NULL, '{\"title\":\"Support Payment of Tfare to Abeokuta for NACOS\",\"amount\":\"40000\"}', '2026-07-19 18:53:20'),
(18, 1, 'users', 6, '{\"action\":\"status_change\"}', '{\"status\":\"active\"}', '2026-07-27 15:29:29'),
(19, 1, 'users', 5, '{\"action\":\"status_change\"}', '{\"status\":\"active\"}', '2026-07-27 15:29:37');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `title` varchar(180) NOT NULL,
  `credit_units` tinyint(4) NOT NULL DEFAULT 3,
  `level` varchar(20) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `code`, `title`, `credit_units`, `level`, `semester`, `status`, `created_at`) VALUES
(1, 'COM 311', 'Database Management Systems', 3, 'HND1', 'First', 'active', '2026-07-08 18:30:55'),
(2, 'COM 312', 'Software Engineering', 3, 'HND1', 'First', 'active', '2026-07-08 18:30:55'),
(3, 'COM 313', 'Web Application Development', 3, 'HND1', 'First', 'active', '2026-07-08 18:30:55'),
(4, 'COM 221', 'Data Structures', 3, 'ND2', 'Second', 'active', '2026-07-08 18:30:55'),
(6, 'COM 101', 'Introduction to Computing', 3, 'ND1', 'First', 'active', '2026-07-08 22:46:26'),
(7, 'COM 111', 'Introduction to Programming', 3, 'ND2', 'First', 'active', '2026-07-19 18:46:15');

-- --------------------------------------------------------

--
-- Table structure for table `course_levels`
--

CREATE TABLE `course_levels` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `code` varchar(30) NOT NULL,
  `institution` varchar(160) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `code`, `institution`, `created_at`) VALUES
(1, 'Computer Science Department', 'CSC', 'Petroleum Training Institute', '2026-07-08 18:30:54');

-- --------------------------------------------------------

--
-- Table structure for table `document_requirements`
--

CREATE TABLE `document_requirements` (
  `id` int(11) NOT NULL,
  `audience` enum('student','staff','all') NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `allowed_extensions` varchar(120) DEFAULT 'jpg,jpeg,png,pdf',
  `max_size_mb` tinyint(4) DEFAULT 2,
  `is_required` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_requirements`
--

INSERT INTO `document_requirements` (`id`, `audience`, `name`, `description`, `allowed_extensions`, `max_size_mb`, `is_required`, `is_active`) VALUES
(1, 'student', 'Passport Photograph', 'Recent passport photograph for student records.', 'jpg,jpeg,png,pdf', 2, 1, 1),
(2, 'student', 'Admission Letter', 'Admission letter or departmental clearance evidence.', 'jpg,jpeg,png,pdf', 2, 1, 1),
(3, 'staff', 'Curriculum Vitae', 'Updated academic and professional CV.', 'jpg,jpeg,png,pdf', 2, 1, 1),
(4, 'staff', 'Highest Qualification', 'Certificate or statement of result.', 'jpg,jpeg,png,pdf', 2, 1, 1),
(5, 'student', 'Upload Registration Form', '', 'jpg,jpeg,png,pdf', 2, 1, 1),
(6, 'student', 'Upload Student Course Form', 'Make sure it is neat', 'jpg,jpeg,png,pdf,docx', 2, 1, 1),
(7, 'staff', 'Upload O Level Result', '', 'jpg,jpeg,png,pdf', 2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `document_uploads`
--

CREATE TABLE `document_uploads` (
  `id` int(11) NOT NULL,
  `requirement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_uploads`
--

INSERT INTO `document_uploads` (`id`, `requirement_id`, `user_id`, `file_path`, `status`, `created_at`) VALUES
(1, 5, 3, 'doc_6a4ee245b4e63_1783554629.pdf', 'approved', '2026-07-08 23:50:29'),
(2, 2, 3, 'doc_6a5d2078ad116_1784488056.pdf', 'pending', '2026-07-19 19:07:36'),
(3, 3, 2, 'doc_6a67415732c77_1785151831.pdf', 'pending', '2026-07-27 11:30:31');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(160) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `expense_date` date NOT NULL,
  `status` enum('draft','approved','rejected') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `category_id`, `title`, `amount`, `expense_date`, `status`, `created_by`, `approved_by`, `created_at`) VALUES
(1, 2, 'Printing course allocation sheets', 8500.00, '2026-07-08', 'approved', 1, 1, '2026-07-08 18:30:55'),
(2, 3, 'Support Payment of Tfare to Abeokuta for NACOS', 40000.00, '2026-07-10', 'approved', 1, NULL, '2026-07-19 18:53:20');

-- --------------------------------------------------------

--
-- Table structure for table `expense_attachments`
--

CREATE TABLE `expense_attachments` (
  `id` int(11) NOT NULL,
  `expense_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `is_system` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `is_system`, `created_at`) VALUES
(1, 'Maintenance', 1, '2026-07-08 18:30:55'),
(2, 'Academic Materials', 1, '2026-07-08 18:30:55'),
(3, 'Logistics', 1, '2026-07-08 18:30:55'),
(4, 'Custom', 0, '2026-07-08 18:30:55');

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`id`, `name`) VALUES
(3, 'HND1'),
(4, 'HND2'),
(1, 'ND1'),
(2, 'ND2');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(160) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `successful` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(160) NOT NULL,
  `body` text NOT NULL,
  `type` varchar(60) DEFAULT 'info',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `body`, `type`, `created_at`) VALUES
(1, 3, 'Payment Successful', 'Your payment of NGN 50,000.00 was successful. Receipt No: RCPT-FDEF5921', 'info', '2026-07-09 02:53:21'),
(2, 1, 'Payment Received', 'A payment of NGN 50,000.00 was just received from Tega Student.', 'info', '2026-07-09 02:53:21'),
(3, 3, 'New Course Added', 'A new course (COM 111 - Introduction to Programming) has been added to the curriculum.', 'info', '2026-07-19 18:46:15'),
(4, 4, 'New Course Added', 'A new course (COM 111 - Introduction to Programming) has been added to the curriculum.', 'info', '2026-07-19 18:46:15'),
(5, 2, 'New Class Assigned', 'You have been assigned to teach COM 111 on Wednesday.', 'info', '2026-07-19 18:47:43'),
(6, 3, 'New Fee Added', 'A new fee (Payment of Report Book - NGN 5,000.00) has been added.', 'info', '2026-07-19 18:51:50'),
(7, 4, 'New Fee Added', 'A new fee (Payment of Report Book - NGN 5,000.00) has been added.', 'info', '2026-07-19 18:51:50'),
(8, 3, 'Payment Successful', 'Your payment of NGN 5,000.00 was successful. Receipt No: RCPT-C7881A37', 'info', '2026-07-19 19:06:46'),
(9, 1, 'Payment Received', 'A payment of NGN 5,000.00 was just received from Tega Student.', 'info', '2026-07-19 19:06:46');

-- --------------------------------------------------------

--
-- Table structure for table `notification_reads`
--

CREATE TABLE `notification_reads` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(160) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_item_id` int(11) DEFAULT NULL,
  `gateway` varchar(60) NOT NULL DEFAULT 'Remita',
  `reference` varchar(120) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('pending','paid','failed','reversed') NOT NULL DEFAULT 'pending',
  `gateway_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_payload`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `payment_item_id`, `gateway`, `reference`, `amount`, `status`, `gateway_payload`, `paid_at`, `created_at`) VALUES
(1, 3, 1, 'Remita', 'RMT-SEED-0001', 15000.00, 'paid', NULL, '2026-07-08 18:30:55', '2026-07-08 18:30:55'),
(2, 3, NULL, 'Remita', 'RMT-2C0E2F5F95', 20000.00, 'failed', NULL, NULL, '2026-07-08 22:05:22'),
(3, 3, 2, 'Remita', 'RMT-DF009787B11B', 25000.00, 'paid', NULL, '2026-07-09 00:56:31', '2026-07-09 00:56:25'),
(4, 3, 3, 'Remita', 'RMT-6ABAE35BB6E7', 10000.00, 'paid', NULL, '2026-07-09 01:57:20', '2026-07-09 01:24:57'),
(5, 3, 4, 'Paystack', 'PAY-C53F633D79B1', 50000.00, 'paid', NULL, '2026-07-09 02:53:21', '2026-07-09 02:53:12'),
(6, 3, 5, 'Paystack', 'PAY-E1E8E58E93A4', 5000.00, 'paid', NULL, '2026-07-19 19:06:46', '2026-07-19 19:05:44');

-- --------------------------------------------------------

--
-- Table structure for table `payment_items`
--

CREATE TABLE `payment_items` (
  `id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_items`
--

INSERT INTO `payment_items` (`id`, `name`, `amount`, `is_active`) VALUES
(1, 'Departmental Due', 15000.00, 1),
(2, 'Project Defense Fee', 25000.00, 1),
(3, 'Seminar Defense Fee', 10000.00, 1),
(4, 'Convocation Fees', 50000.00, 1),
(5, 'Payment of Report Book', 5000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment_webhooks`
--

CREATE TABLE `payment_webhooks` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `gateway` varchar(60) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qualifications`
--

CREATE TABLE `qualifications` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `qualification` varchar(160) NOT NULL,
  `institution` varchar(160) NOT NULL,
  `year_awarded` year(4) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qualifications`
--

INSERT INTO `qualifications` (`id`, `staff_id`, `qualification`, `institution`, `year_awarded`, `file_path`) VALUES
(1, 1, 'MSc Computer Science', 'University of Benin', '2021', NULL),
(2, 1, 'BSc  Computer Science', 'University of Benin', '2019', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `receipt_no` varchar(80) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `payment_id`, `receipt_no`, `file_path`, `issued_at`) VALUES
(1, 3, 'RCPT-F767C726', NULL, '2026-07-09 00:56:31'),
(2, 4, 'RCPT-A663309E', NULL, '2026-07-09 01:57:20'),
(3, 5, 'RCPT-FDEF5921', NULL, '2026-07-09 02:53:21'),
(4, 6, 'RCPT-C7881A37', NULL, '2026-07-19 19:06:46');

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_exports`
--

CREATE TABLE `report_exports` (
  `id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `generated_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `slug` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`) VALUES
(1, 'Administrator', 'admin'),
(2, 'Staff', 'staff'),
(3, 'Student', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `is_current` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `name`, `is_current`) VALUES
(1, 'First', 1),
(2, 'Second', 0);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `payload` text DEFAULT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(120) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `is_secret` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `is_secret`) VALUES
(1, 'department_name', 'Computer Science Department', 0),
(2, 'remita_merchant_id', '', 1),
(3, 'remita_api_key', '', 1),
(4, 'session_timeout_minutes', '30', 0);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `staff_no` varchar(60) DEFAULT NULL,
  `designation` varchar(120) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `office` varchar(120) DEFAULT NULL,
  `passport_path` varchar(255) DEFAULT NULL,
  `cv_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `user_id`, `staff_no`, `designation`, `phone`, `office`, `passport_path`, `cv_path`, `created_at`) VALUES
(1, 2, 'CSC/STF/001', 'Lecturer I', '08030000001', 'Block A, Room 4', NULL, NULL, '2026-07-08 18:30:55'),
(2, 5, '1111111', 'lab tech', '0000000', NULL, NULL, NULL, '2026-07-27 15:27:27');

-- --------------------------------------------------------

--
-- Table structure for table `staff_course_assignments`
--

CREATE TABLE `staff_course_assignments` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `academic_session_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_documents`
--

CREATE TABLE `staff_documents` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `matric_no` varchar(60) DEFAULT NULL,
  `level` varchar(20) NOT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `passport_path` varchar(255) DEFAULT NULL,
  `admission_year` year(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `matric_no`, `level`, `phone`, `address`, `passport_path`, `admission_year`, `created_at`) VALUES
(1, 3, 'M.24/HND/SWD/11579', 'HND1', '08030000002', 'Effurun, Delta State', NULL, '2024', '2026-07-08 18:30:55'),
(2, 4, 'MAT999', 'ND1', '1234567890', 'New Address', NULL, NULL, '2026-07-08 20:30:05'),
(3, 6, 'stu11111', 'ND1', '0000000', '1 pti rd', NULL, NULL, '2026-07-27 15:28:48');

-- --------------------------------------------------------

--
-- Table structure for table `student_documents`
--

CREATE TABLE `student_documents` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_backups`
--

CREATE TABLE `system_backups` (
  `id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` varchar(60) DEFAULT 'created',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue` varchar(120) NOT NULL,
  `level` varchar(20) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `course_id`, `staff_id`, `day_of_week`, `start_time`, `end_time`, `venue`, `level`, `semester`, `created_at`) VALUES
(1, 1, 1, 'Monday', '09:00:00', '11:00:00', 'Lecture Hall A', 'HND1', 'First', '2026-07-08 18:30:55'),
(2, 2, 1, 'Friday', '12:00:00', '14:00:00', 'Computer Lab 1', 'HND1', 'First', '2026-07-08 18:30:55'),
(3, 6, 1, 'Wednesday', '13:00:00', '15:00:00', 'SH 2', 'ND1', 'First', '2026-07-08 22:48:28'),
(4, 7, 1, 'Wednesday', '09:00:00', '11:00:00', 'SH 2', 'ND2', 'Second', '2026-07-19 18:47:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','active','inactive','suspended') NOT NULL DEFAULT 'pending',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `password`, `status`, `email_verified_at`, `last_login_at`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Department Admin', 'admin@dms.test', '$2y$10$lG14oyLc0oSVx6bdTjQaSuqOJ/nDsSndzeCqXB1jDcWuY3ieVQ0kq', 'active', '2026-07-08 18:30:54', '2026-09-23 18:27:00', NULL, '2026-07-08 18:30:54', '2026-09-23 18:27:00'),
(2, 2, 'Ada Lecturer', 'staff@dms.test', '$2y$10$lG14oyLc0oSVx6bdTjQaSuqOJ/nDsSndzeCqXB1jDcWuY3ieVQ0kq', 'active', '2026-07-08 18:30:54', '2026-07-27 14:46:18', NULL, '2026-07-08 18:30:54', '2026-07-27 14:46:18'),
(3, 3, 'Tega Student', 'student@dms.test', '$2y$10$lG14oyLc0oSVx6bdTjQaSuqOJ/nDsSndzeCqXB1jDcWuY3ieVQ0kq', 'active', '2026-07-08 18:30:54', '2026-07-27 14:36:15', NULL, '2026-07-08 18:30:54', '2026-07-27 14:36:15'),
(4, 3, 'New Student', 'newstudent@dms.test', '$2y$10$lG14oyLc0oSVx6bdTjQaSuqOJ/nDsSndzeCqXB1jDcWuY3ieVQ0kq', 'pending', NULL, NULL, NULL, '2026-07-08 20:30:05', '2026-07-08 20:30:05'),
(5, 2, 'staff1', 'staff1@dms.test', '$2y$10$OdPgnYoPsLEZbD0tlwZaa.trygVc.9ZKQIAiS9dJkAXZRZ3MgRQka', 'active', NULL, NULL, NULL, '2026-07-27 15:27:27', '2026-07-27 15:29:37'),
(6, 3, 'student1', 'student1@dms.test', '$2y$10$zYAAN7nYGt7D6CFacXec1OmCKYtMwwjv/p/6qgT0HSpwsQEZVSGLy', 'active', NULL, NULL, NULL, '2026-07-27 15:28:48', '2026-07-27 15:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `user_status_history`
--

CREATE TABLE `user_status_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `previous_status` varchar(40) DEFAULT NULL,
  `new_status` varchar(40) NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `capacity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `name`, `capacity`) VALUES
(1, 'Lecture Hall A', 120),
(2, 'Computer Lab 1', 60);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_sessions`
--
ALTER TABLE `academic_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `announcement_roles`
--
ALTER TABLE `announcement_roles`
  ADD PRIMARY KEY (`announcement_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `course_levels`
--
ALTER TABLE `course_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `document_requirements`
--
ALTER TABLE `document_requirements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_uploads`
--
ALTER TABLE `document_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requirement_id` (`requirement_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `expense_attachments`
--
ALTER TABLE `expense_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_id` (`expense_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_login_attempts_email` (`email`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notification_reads`
--
ALTER TABLE `notification_reads`
  ADD PRIMARY KEY (`notification_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_password_resets_email` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `idx_payments_status` (`status`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payment_item_id` (`payment_item_id`);

--
-- Indexes for table `payment_items`
--
ALTER TABLE `payment_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_webhooks`
--
ALTER TABLE `payment_webhooks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `qualifications`
--
ALTER TABLE `qualifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_id` (`payment_id`),
  ADD UNIQUE KEY `receipt_no` (`receipt_no`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `report_exports`
--
ALTER TABLE `report_exports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `generated_by` (`generated_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `staff_no` (`staff_no`);

--
-- Indexes for table `staff_course_assignments`
--
ALTER TABLE `staff_course_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_staff_course` (`staff_id`,`course_id`,`academic_session_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `academic_session_id` (`academic_session_id`);

--
-- Indexes for table `staff_documents`
--
ALTER TABLE `staff_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `matric_no` (`matric_no`);

--
-- Indexes for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `system_backups`
--
ALTER TABLE `system_backups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_timetable_day` (`day_of_week`,`start_time`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_status` (`status`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_status_history`
--
ALTER TABLE `user_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_sessions`
--
ALTER TABLE `academic_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_levels`
--
ALTER TABLE `course_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `document_requirements`
--
ALTER TABLE `document_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `document_uploads`
--
ALTER TABLE `document_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expense_attachments`
--
ALTER TABLE `expense_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment_items`
--
ALTER TABLE `payment_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_webhooks`
--
ALTER TABLE `payment_webhooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qualifications`
--
ALTER TABLE `qualifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_exports`
--
ALTER TABLE `report_exports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff_course_assignments`
--
ALTER TABLE `staff_course_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_documents`
--
ALTER TABLE `staff_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_documents`
--
ALTER TABLE `student_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_backups`
--
ALTER TABLE `system_backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_status_history`
--
ALTER TABLE `user_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `announcement_roles`
--
ALTER TABLE `announcement_roles`
  ADD CONSTRAINT `announcement_roles_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcement_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `document_uploads`
--
ALTER TABLE `document_uploads`
  ADD CONSTRAINT `document_uploads_ibfk_1` FOREIGN KEY (`requirement_id`) REFERENCES `document_requirements` (`id`),
  ADD CONSTRAINT `document_uploads_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`),
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `expenses_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expense_attachments`
--
ALTER TABLE `expense_attachments`
  ADD CONSTRAINT `expense_attachments_ibfk_1` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_reads`
--
ALTER TABLE `notification_reads`
  ADD CONSTRAINT `notification_reads_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_reads_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`payment_item_id`) REFERENCES `payment_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment_webhooks`
--
ALTER TABLE `payment_webhooks`
  ADD CONSTRAINT `payment_webhooks_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `qualifications`
--
ALTER TABLE `qualifications`
  ADD CONSTRAINT `qualifications_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report_exports`
--
ALTER TABLE `report_exports`
  ADD CONSTRAINT `report_exports_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_course_assignments`
--
ALTER TABLE `staff_course_assignments`
  ADD CONSTRAINT `staff_course_assignments_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_course_assignments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_course_assignments_ibfk_3` FOREIGN KEY (`academic_session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `staff_documents`
--
ALTER TABLE `staff_documents`
  ADD CONSTRAINT `staff_documents_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD CONSTRAINT `student_documents_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_documents_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timetable_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `user_status_history`
--
ALTER TABLE `user_status_history`
  ADD CONSTRAINT `user_status_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
