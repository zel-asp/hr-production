-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Mar 07, 2026 at 07:20 AM
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
-- Database: `hrdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_notes`
--

CREATE TABLE `admin_notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `note_title` varchar(150) DEFAULT NULL,
  `note_content` text NOT NULL,
  `note_type` enum('general','disciplinary','performance','confidential') DEFAULT 'general',
  `status` enum('active','archived') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notes`
--

INSERT INTO `admin_notes` (`id`, `employee_id`, `note_title`, `note_content`, `note_type`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(6, 62, 'Missing Requirements - 2026-03-07', 'Employee: Sasuke (EMP-056)\nMissing Requirements: 1\n\nMissing Items:\n1. Resume\n\nNotification sent on: 2026-03-07 07:19:59', 'confidential', 'active', NULL, '2026-03-07 06:19:59', '2026-03-07 06:19:59');

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `position` varchar(50) NOT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `status` enum('New','Review','Interview','Offer','Rejected','Hired') NOT NULL DEFAULT 'New',
  `hired_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `cover_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`id`, `full_name`, `department`, `email`, `phone`, `position`, `experience`, `education`, `skills`, `status`, `hired_date`, `start_date`, `resume_path`, `cover_note`, `created_at`, `age`, `gender`) VALUES
(56, 'Uzumaki Dela CRUZ', 'Finance', 'B0s5ls.Do1s@gmail.com', '09123456789', 'Restaurant Server', 's', 'xcv', 'd', 'Hired', '2026-03-05', '2026-03-15', '', '', '2026-03-04 07:37:29', 34, 'male'),
(57, 'Uzumaki Dela CRUZ', 'Finance', 'B0s5ls.sDo1s@gmail.com', '09123456789', 'Restaurant Serversf', 's', 'xcv', 'a', 'Hired', '2026-03-06', '2026-03-07', '', '', '2026-03-04 07:37:41', 34, 'female'),
(58, 'Uzumaki Dela CRUZ', 'Finance', 'B0s5ls.Dl1s@gmail.com', '09565819961', 'Restaurant Serversf', 'sdsd', 'e2432', '3423', 'Hired', '2026-03-07', '2026-03-08', '', 'd', '2026-03-06 03:00:13', 18, 'male'),
(59, 'hello admin', 'Management', 'janzeldols@gmail.com', '09123456789', 'Restaurant Serversfafasfasf', 'fsd', 'asa', 'asa', 'Hired', '2026-03-07', '2026-03-08', 'https://plxoonwsguadkqisevxh.supabase.co/storage/v1/object/public/resumes/1772823342366-s2wcd5yx0z.jpg', 'asa', '2026-03-06 18:55:43', 19, 'female');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(10) UNSIGNED DEFAULT NULL,
  `clock_in` datetime NOT NULL,
  `clock_out` datetime DEFAULT NULL,
  `pause_start` datetime DEFAULT NULL,
  `pause_total` int(11) DEFAULT 0,
  `late_minutes` int(11) DEFAULT 0,
  `late_status` enum('on_time','grace_period','late') DEFAULT 'on_time',
  `regular_hours` decimal(5,2) DEFAULT 0.00,
  `overtime_hours` decimal(5,2) DEFAULT 0.00,
  `early_departure_minutes` int(11) DEFAULT 0,
  `status` enum('clocked_in','paused','clocked_out') DEFAULT 'clocked_out',
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `shift_id`, `clock_in`, `clock_out`, `pause_start`, `pause_total`, `late_minutes`, `late_status`, `regular_hours`, `overtime_hours`, `early_departure_minutes`, `status`, `date`, `created_at`, `updated_at`) VALUES
(132, 62, 1, '2026-03-09 22:06:15', '2026-03-09 22:06:20', NULL, 0, 966, 'late', 0.00, 0.00, 0, 'clocked_out', '2026-03-09', '2026-03-09 14:06:15', '2026-03-09 14:06:20'),
(133, 62, 1, '2026-03-10 00:15:52', NULL, NULL, 0, 0, 'on_time', 0.00, 0.00, 0, 'clocked_in', '2026-03-10', '2026-03-09 16:15:52', '2026-03-09 16:15:52');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_summary`
--

CREATE TABLE `attendance_summary` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `total_regular_hours` decimal(7,2) DEFAULT 0.00,
  `total_overtime_hours` decimal(7,2) DEFAULT 0.00,
  `total_late_minutes` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_summary`
--

INSERT INTO `attendance_summary` (`id`, `employee_id`, `period_start`, `period_end`, `total_regular_hours`, `total_overtime_hours`, `total_late_minutes`, `created_at`, `updated_at`) VALUES
(38, 62, '2026-03-06', '2026-03-20', 0.00, 0.00, 966, '2026-03-09 14:06:20', '2026-03-09 14:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `benefit_providers`
--

CREATE TABLE `benefit_providers` (
  `id` int(10) UNSIGNED NOT NULL,
  `provider_name` varchar(100) NOT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `benefit_providers`
--

INSERT INTO `benefit_providers` (`id`, `provider_name`, `contact_info`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Maxicare', 'info@maxicare.com.ph', 'HMO provider', '2026-02-26 13:18:09', '2026-02-26 13:18:09'),
(2, 'Medicard', 'support@medicard.com.ph', 'HMO provider', '2026-02-26 13:18:09', '2026-02-26 13:18:09'),
(3, 'Intellicare', 'contact@intellicare.com.ph', 'HMO provider', '2026-02-26 13:18:09', '2026-02-26 13:18:09'),
(4, 'AXA', 'service@axa.com.ph', 'Insurance provider', '2026-02-26 13:18:09', '2026-02-26 13:18:09');

-- --------------------------------------------------------

--
-- Table structure for table `compensation_reviews`
--

CREATE TABLE `compensation_reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `current_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `review_type` enum('annual','promotion','merit','market') NOT NULL,
  `review_date` date NOT NULL,
  `effective_date` date NOT NULL,
  `proposed_salary` decimal(10,2) NOT NULL,
  `increase_amount` decimal(10,2) GENERATED ALWAYS AS (`proposed_salary` - `current_salary`) STORED,
  `increase_percentage` decimal(6,2) GENERATED ALWAYS AS (round((`proposed_salary` - `current_salary`) / `current_salary` * 100,2)) STORED,
  `status` enum('draft','pending_finance','approved','rejected') DEFAULT 'draft',
  `finance_approved_at` datetime DEFAULT NULL,
  `finance_approved_by` int(11) DEFAULT NULL,
  `finance_notes` text DEFAULT NULL,
  `budget_code` varchar(50) DEFAULT NULL,
  `annual_impact` decimal(10,2) GENERATED ALWAYS AS ((`proposed_salary` - `current_salary`) * 12) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compensation_reviews`
--

INSERT INTO `compensation_reviews` (`id`, `employee_id`, `current_salary`, `review_type`, `review_date`, `effective_date`, `proposed_salary`, `status`, `finance_approved_at`, `finance_approved_by`, `finance_notes`, `budget_code`, `created_at`, `updated_at`, `created_by`) VALUES
(5, 69, 42240.00, 'promotion', '2026-03-07', '2026-03-08', 1213232.00, 'draft', NULL, NULL, '', NULL, '2026-03-07 02:06:03', '2026-03-07 02:06:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `competencies`
--

CREATE TABLE `competencies` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `required_level` tinyint(4) NOT NULL DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competencies`
--

INSERT INTO `competencies` (`id`, `name`, `description`, `created_at`, `required_level`) VALUES
(1, 'Customer Service', NULL, '2026-03-02 06:32:29', 3),
(2, 'Food Safety', NULL, '2026-03-02 06:32:29', 4),
(3, 'POS Systems', NULL, '2026-03-02 06:32:29', 2),
(4, 'Team Leadership', NULL, '2026-03-02 06:32:29', 3);

-- --------------------------------------------------------

--
-- Table structure for table `competency_assessments`
--

CREATE TABLE `competency_assessments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `competency_id` int(11) NOT NULL,
  `assessor_id` int(11) NOT NULL,
  `proficiency_level` tinyint(4) NOT NULL CHECK (`proficiency_level` between 1 and 5),
  `assessment_notes` text DEFAULT NULL,
  `assessment_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Passed','Needs Improvement') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `applicant_id` int(10) UNSIGNED DEFAULT NULL,
  `employee_number` varchar(10) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `position` varchar(100) NOT NULL,
  `hourly_rate` decimal(10,2) DEFAULT 0.00,
  `department` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `hired_date` date DEFAULT NULL,
  `onboarding_status` enum('Onboarding','In Progress','Onboarded') DEFAULT 'Onboarding',
  `status` varchar(50) NOT NULL DEFAULT 'new',
  `shift_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `evaluation_status` varchar(50) DEFAULT 'Pending',
  `role` enum('employee','mentor','evaluator','admin') DEFAULT 'employee',
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `benefit_status` varchar(50) DEFAULT 'Not Enrolled',
  `resume` varchar(255) DEFAULT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `nbi_clearance` varchar(255) DEFAULT NULL,
  `medical_result` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `applicant_id`, `employee_number`, `full_name`, `email`, `phone`, `position`, `hourly_rate`, `department`, `start_date`, `hired_date`, `onboarding_status`, `status`, `shift_id`, `created_at`, `updated_at`, `evaluation_status`, `role`, `age`, `gender`, `benefit_status`, `resume`, `birth_certificate`, `nbi_clearance`, `medical_result`) VALUES
(62, 56, 'EMP-056', 'Sasuke', 'B0s5ls.Do1s@gmail.com', '09123456789', 'Restaurant Server', 250.00, 'Logistic', '2026-03-15', '2026-03-05', 'In Progress', 'Probationary', 1, '2026-03-05 20:15:52', '2026-03-07 14:19:52', 'Pending', 'employee', 34, 'male', 'enrolled', '', 'https://plxoonwsguadkqisevxh.supabase.co/storage/v1/object/public/birth-certificate/birth_certificate_1772850516408.png', 'https://plxoonwsguadkqisevxh.supabase.co/storage/v1/object/public/nbi-clearance/nbi_clearance_1772850555521.png', 'https://plxoonwsguadkqisevxh.supabase.co/storage/v1/object/public/medical/medical_result_1772850506178.png'),
(63, 57, 'EMP-057', 'Sakura', 'B0s5ls.sDo1s@gmail.com', '09123456789', 'Senior one', 400.00, 'Logistic', '2026-03-07', '2026-03-06', 'In Progress', 'Probationary', 1, '2026-03-06 09:24:58', '2026-03-07 12:00:29', 'Evaluated', 'mentor', 34, 'female', 'Not Enrolled', NULL, NULL, NULL, NULL),
(65, NULL, 'EMP-67', 'Michael Reyes', 'michael.reyes@company.com', '09171234567', 'Restaurant Manager', 350.00, 'Management', '2025-01-15', '2025-01-15', 'Onboarded', 'Regular', 1, '2026-03-09 23:01:45', '2026-03-07 02:56:21', 'Evaluated', 'admin', 42, 'male', 'enrolled', NULL, NULL, NULL, NULL),
(66, NULL, 'EMP-060', 'Jennifer Santos', 'jennifer.santos@company.com', '09181234568', 'Senior Server', 250.00, 'F&B Service', '2025-02-01', '2025-02-01', 'Onboarded', 'Regular', 2, '2026-03-09 23:01:45', '2026-03-09 23:04:29', 'Evaluated', 'mentor', 35, 'female', 'enrolled', NULL, NULL, NULL, NULL),
(67, NULL, 'EMP-061', 'Robert Gomez', 'robert.gomez@company.com', '09191234569', 'Shift Manager', 300.00, 'Operations', '2025-01-20', '2025-01-20', 'Onboarded', 'Regular', 3, '2026-03-09 23:01:45', '2026-03-09 23:01:45', 'Evaluated', 'mentor', 38, 'male', 'enrolled', NULL, NULL, NULL, NULL),
(68, NULL, 'EMP-062', 'Maria Lopez', 'maria.lopez@company.com', '09201234570', 'Head Server', 220.00, 'F&B Service', '2025-02-15', '2025-02-15', 'Onboarded', 'Regular', 1, '2026-03-09 23:01:45', '2026-03-09 23:04:12', 'Evaluated', 'evaluator', 29, 'female', 'enrolled', NULL, NULL, NULL, NULL),
(69, NULL, 'EMP-063', 'Antonio Villanueva', 'antonio.v@company.com', '09211234571', 'Senior Server', 240.00, 'F&B Service', '2025-03-01', '2025-03-01', 'Onboarded', 'Regular', 2, '2026-03-09 23:01:45', '2026-03-09 23:04:33', 'Evaluated', 'evaluator', 31, 'male', 'enrolled', NULL, NULL, NULL, NULL),
(70, 58, 'EMP-058', 'Uzumaki Dela CRUZ', 'B0s5ls.Dl1s@gmail.com', '09565819961', 'Restaurant Serversf', 0.00, 'Finance', '2026-03-08', '2026-03-06', 'Onboarding', 'Probationary', NULL, '2026-03-07 02:09:21', '2026-03-07 02:09:21', 'Pending', 'employee', 18, 'male', 'Not Enrolled', NULL, NULL, NULL, NULL),
(73, 59, 'EMP-059', 'hello admin', 'janzeldols@gmail.com', '09123456789', 'Restaurant Serversfafasfasf', 0.00, 'Management', '2026-03-08', '2026-03-06', 'Onboarding', 'Probationary', NULL, '2026-03-07 03:31:25', '2026-03-07 03:31:25', 'Pending', 'employee', 19, 'female', 'Not Enrolled', 'https://plxoonwsguadkqisevxh.supabase.co/storage/v1/object/public/resumes/1772823342366-s2wcd5yx0z.jpg', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_accounts`
--

CREATE TABLE `employee_accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `applicant_id` int(10) UNSIGNED NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `account_status` enum('Active','Inactive','Suspended') NOT NULL DEFAULT 'Active',
  `generated_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `session_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_accounts`
--

INSERT INTO `employee_accounts` (`id`, `applicant_id`, `employee_id`, `username`, `password`, `email`, `account_status`, `generated_date`, `last_login`, `department`, `session_token`) VALUES
(37, 56, 'EMP-056', 'B0s5ls.Do1s', '$2y$10$/XwXWVPHji8aBSHuOAKLPeO2eEVsGh7to9gFrd59gyOyX5y3yhl8.', 'B0s5ls.Do1s@gmail.com', 'Active', '2026-03-04 07:38:03', '2026-03-07 06:20:04', 'Finance', '55259ea2d92e1342afbecb28bd33e70fcc9c87a816dd9dc5f8099fa57b64a805'),
(40, 57, 'EMP-057', 'B0s5ls.sDo1s', '$2y$10$dzrTjttLuXqboeAijQ1MjezgFLw6ES0eR97vtrnJwqQ0mTUuo3rHW', 'B0s5ls.sDo1s@gmail.com', 'Active', '2026-03-06 01:11:08', '2026-03-06 03:13:49', 'Finance', '4b2535022c5005fa1f17016c1ecfddf5ec68e8f8e9a85e7d83c3bf83231f891a'),
(41, 58, 'EMP-058', 'B0s5ls.Dl1s', '$2y$10$y4OK7yjlDvKabZzuRL24mOBVIPaxtfj/BWfavgLuQfZ.UnHwgI8Xa', 'B0s5ls.Dl1s@gmail.com', 'Active', '2026-03-06 03:01:54', NULL, 'Finance', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_benefits`
--

CREATE TABLE `employee_benefits` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `benefit_type` varchar(100) NOT NULL,
  `provider_id` int(10) UNSIGNED NOT NULL,
  `effective_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `coverage_amount` decimal(15,2) DEFAULT NULL,
  `monthly_premium` decimal(10,2) DEFAULT NULL,
  `dependents` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_benefits`
--

INSERT INTO `employee_benefits` (`id`, `employee_id`, `benefit_type`, `provider_id`, `effective_date`, `expiry_date`, `coverage_amount`, `monthly_premium`, `dependents`, `created_at`, `updated_at`) VALUES
(13, 62, 'HMO - Principal', 3, '2026-03-28', '2026-03-28', 123456.00, 1234.00, NULL, '2026-03-05 12:38:16', '2026-03-05 12:38:16');

-- --------------------------------------------------------

--
-- Table structure for table `employee_recognitions`
--

CREATE TABLE `employee_recognitions` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `recognition_type` enum('Employee of the Month','Rising Star','Perfect Attendance','Innovation Award','Team Player') NOT NULL,
  `performance_highlight` text DEFAULT NULL,
  `recognized_by` int(11) DEFAULT NULL,
  `recognition_date` date DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_schedules`
--

CREATE TABLE `employee_schedules` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(10) UNSIGNED DEFAULT NULL,
  `schedule_date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `shift_code` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `status` enum('scheduled','cancelled','completed') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_claims`
--

CREATE TABLE `expense_claims` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `expense_date` date NOT NULL,
  `category` varchar(100) NOT NULL,
  `merchant` varchar(255) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `project` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `receipt_path` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_claims`
--

INSERT INTO `expense_claims` (`id`, `employee_id`, `expense_date`, `category`, `merchant`, `amount`, `project`, `description`, `receipt_path`, `status`, `approved_by`, `approved_at`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(6, 62, '2026-03-07', 'Transportation', 'd', 123.00, NULL, '3', '', 'Pending', NULL, NULL, NULL, '2026-03-06 01:16:51', '2026-03-06 01:16:51');

-- --------------------------------------------------------

--
-- Table structure for table `job_postings`
--

CREATE TABLE `job_postings` (
  `id` int(11) NOT NULL,
  `position` varchar(150) NOT NULL,
  `department` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `shift` varchar(50) NOT NULL,
  `salary` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_postings`
--

INSERT INTO `job_postings` (`id`, `position`, `department`, `location`, `shift`, `salary`, `created_at`, `updated_at`) VALUES
(57, 'Restaurant Serversfafasfasf', 'Management', 'Main Dining Room', 'evening', '$15-20/hr + tips', '2026-03-03 02:42:01', '2026-03-03 02:42:01'),
(58, 'Restaurant Serversf', 'Finance', 'Main Dining Room', 'evening', '₱15-20/hr + tips', '2026-03-03 08:49:13', '2026-03-03 08:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` enum('Annual Leave','Sick Leave','Personal Day','Remote Work') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Cancelled') DEFAULT 'Pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `total_days`, `reason`, `status`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(16, 62, 'Annual Leave', '2026-03-09', '2026-03-10', 2, '', 'Approved', NULL, '2026-03-09 22:09:41', '2026-03-09 14:09:35', '2026-03-09 14:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `mentor_assignments`
--

CREATE TABLE `mentor_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `mentee_employee_id` int(11) NOT NULL,
  `mentor_employee_id` int(11) NOT NULL,
  `program_duration` enum('3 months','6 months','12 months') NOT NULL,
  `goals` text DEFAULT NULL,
  `status` enum('Active','Completed','Cancelled') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentor_assignments`
--

INSERT INTO `mentor_assignments` (`id`, `mentee_employee_id`, `mentor_employee_id`, `program_duration`, `goals`, `status`, `created_at`, `updated_at`) VALUES
(1, 62, 68, '6 months', 'fi', 'Active', '2026-03-09 15:52:13', '2026-03-09 15:52:13'),
(2, 63, 62, '12 months', 'nice', 'Active', '2026-03-09 16:14:51', '2026-03-09 16:14:51');

-- --------------------------------------------------------

--
-- Table structure for table `mentor_ratings`
--

CREATE TABLE `mentor_ratings` (
  `id` int(10) UNSIGNED NOT NULL,
  `mentee_employee_id` int(11) NOT NULL,
  `mentor_employee_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text NOT NULL,
  `rating_date` date DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentor_ratings`
--

INSERT INTO `mentor_ratings` (`id`, `mentee_employee_id`, `mentor_employee_id`, `rating`, `comment`, `rating_date`, `created_at`) VALUES
(1, 63, 62, 5, 'nice effort', '2026-03-10', '2026-03-09 16:28:09'),
(2, 63, 62, 1, 'h', '2026-03-10', '2026-03-09 16:46:09'),
(3, 63, 62, 1, 'd', '2026-03-10', '2026-03-09 16:51:22');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_summary`
--

CREATE TABLE `payroll_summary` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `total_regular_hours` decimal(7,2) DEFAULT 0.00,
  `total_overtime_hours` decimal(7,2) DEFAULT 0.00,
  `hourly_rate` decimal(10,2) DEFAULT 0.00,
  `gross_pay` decimal(12,2) DEFAULT 0.00,
  `total_deductions` decimal(12,2) DEFAULT 0.00,
  `net_pay` decimal(12,2) DEFAULT 0.00,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performance_criteria_scores`
--

CREATE TABLE `performance_criteria_scores` (
  `id` int(10) UNSIGNED NOT NULL,
  `evaluation_id` int(10) UNSIGNED NOT NULL,
  `criteria_number` tinyint(1) NOT NULL,
  `criteria_label` varchar(100) NOT NULL,
  `criteria_description` varchar(255) NOT NULL,
  `score` tinyint(1) NOT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance_criteria_scores`
--

INSERT INTO `performance_criteria_scores` (`id`, `evaluation_id`, `criteria_number`, `criteria_label`, `criteria_description`, `score`, `comments`) VALUES
(236, 48, 1, 'Job Knowledge', 'Understanding of role and standards', 3, ''),
(237, 48, 2, 'Quality of Work', 'Accuracy and attention to detail', 3, ''),
(238, 48, 3, 'Customer Service', 'Customer interaction quality', 5, ''),
(239, 48, 4, 'Teamwork & Collaboration', 'Team cooperation', 4, ''),
(240, 48, 5, 'Attendance & Punctuality', 'Reliability and punctuality', 4, '');

-- --------------------------------------------------------

--
-- Table structure for table `performance_evaluations`
--

CREATE TABLE `performance_evaluations` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `review_period_start` date NOT NULL,
  `review_period_end` date NOT NULL,
  `review_type` varchar(50) NOT NULL DEFAULT '90-Day Probationary Review',
  `overall_score` decimal(3,1) NOT NULL,
  `interpretation` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance_evaluations`
--

INSERT INTO `performance_evaluations` (`id`, `employee_id`, `review_period_start`, `review_period_end`, `review_type`, `overall_score`, `interpretation`, `created_at`, `updated_at`) VALUES
(48, 63, '2026-03-07', '2026-06-04', '90-Day Probationary Review', 3.8, 'Exceeds Expectations', '2026-03-06 03:02:38', '2026-03-09 17:27:20');

-- --------------------------------------------------------

--
-- Table structure for table `performance_improvement_plans`
--

CREATE TABLE `performance_improvement_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `evaluation_id` int(10) UNSIGNED NOT NULL,
  `improvement_areas` text NOT NULL,
  `goal1` varchar(255) DEFAULT NULL,
  `goal2` varchar(255) DEFAULT NULL,
  `goal3` varchar(255) DEFAULT NULL,
  `pip_start_date` date NOT NULL,
  `pip_end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regular_employment`
--

CREATE TABLE `regular_employment` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `evaluation_id` int(10) UNSIGNED NOT NULL,
  `effective_date` date NOT NULL,
  `employment_type` enum('Regular Full-Time','Regular Part-Time') NOT NULL,
  `manager_comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` int(10) UNSIGNED NOT NULL,
  `shift_name` varchar(50) NOT NULL,
  `shift_code` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `grace_period_minutes` int(11) DEFAULT 15,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `shift_name`, `shift_code`, `start_time`, `end_time`, `grace_period_minutes`, `created_at`, `updated_at`) VALUES
(1, 'Morning Shift', 'MORNING', '06:00:00', '14:00:00', 15, '2026-02-28 10:56:01', '2026-02-28 10:56:01'),
(2, 'Afternoon Shift', 'AFTERNOON', '14:00:00', '22:00:00', 15, '2026-02-28 10:56:01', '2026-02-28 10:56:01'),
(3, 'Graveyard Shift', 'GRAVEYARD', '22:00:00', '06:00:00', 15, '2026-02-28 10:56:01', '2026-02-28 10:56:01');

-- --------------------------------------------------------

--
-- Table structure for table `shift_swap_requests`
--

CREATE TABLE `shift_swap_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `requester_employee_id` int(11) NOT NULL,
  `swap_with_employee_id` int(11) NOT NULL,
  `swap_date` date NOT NULL,
  `requester_shift_id` int(10) UNSIGNED DEFAULT NULL,
  `swap_with_shift_id` int(10) UNSIGNED DEFAULT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected','Cancelled') DEFAULT 'Pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shift_swap_requests`
--

INSERT INTO `shift_swap_requests` (`id`, `requester_employee_id`, `swap_with_employee_id`, `swap_date`, `requester_shift_id`, `swap_with_shift_id`, `reason`, `status`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(6, 62, 63, '2026-03-10', 1, 2, 'hi', 'Approved', NULL, '2026-03-08 21:49:15', '2026-03-08 13:49:09', '2026-03-08 13:49:15');

-- --------------------------------------------------------

--
-- Table structure for table `statutory_deductions`
--

CREATE TABLE `statutory_deductions` (
  `id` int(11) NOT NULL,
  `deduction_name` varchar(50) NOT NULL,
  `deduction_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `statutory_deductions`
--

INSERT INTO `statutory_deductions` (`id`, `deduction_name`, `deduction_amount`, `created_at`) VALUES
(1, 'SSS', 450.00, '2026-03-05 12:52:31'),
(2, 'PhilHealth', 250.00, '2026-03-05 12:52:31'),
(3, 'PagIBIG', 100.00, '2026-03-05 12:52:31');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `task_type` varchar(100) NOT NULL,
  `task_description` varchar(255) NOT NULL,
  `due_date` date NOT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `assigned_staff` varchar(100) NOT NULL,
  `status` enum('Not Started','Ongoing','Completed') NOT NULL DEFAULT 'Not Started',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `assigned_to`, `task_type`, `task_description`, `due_date`, `priority`, `assigned_staff`, `status`, `created_at`, `updated_at`) VALUES
(41, 62, 'paperwork', 'dasd', '2026-03-07', 'high', 'Lisa Martinez', 'Completed', '2026-03-06 03:05:58', '2026-03-06 03:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `training_providers`
--

CREATE TABLE `training_providers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('internal','external','certification') NOT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_providers`
--

INSERT INTO `training_providers` (`id`, `name`, `type`, `contact_info`, `created_at`, `updated_at`) VALUES
(1, 'SafeFood Certification Inc.', 'external', NULL, '2026-03-02 08:11:42', '2026-03-02 08:11:42'),
(2, 'Hospitality Training Institute', 'external', NULL, '2026-03-02 08:11:42', '2026-03-02 08:11:42'),
(3, 'Leadership Academy International', 'external', NULL, '2026-03-02 08:11:42', '2026-03-02 08:11:42'),
(4, 'TechSkills Learning Center', 'external', NULL, '2026-03-02 08:11:42', '2026-03-02 08:11:42');

-- --------------------------------------------------------

--
-- Table structure for table `training_schedule`
--

CREATE TABLE `training_schedule` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `training_type` enum('internal','external','certification') NOT NULL,
  `competency_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `employee_id` int(11) NOT NULL,
  `status` enum('Scheduled','Completed','Missed') DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `provider_id` int(11) DEFAULT NULL,
  `assessment_status` enum('pending','completed','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_schedule`
--

INSERT INTO `training_schedule` (`id`, `title`, `training_type`, `competency_id`, `start_date`, `end_date`, `start_time`, `end_time`, `venue`, `employee_id`, `status`, `created_at`, `updated_at`, `provider_id`, `assessment_status`) VALUES
(17, '', 'internal', 1, '2026-03-06', '2026-03-07', '11:06:00', '11:07:00', 'dfd', 63, 'Completed', '2026-03-06 03:07:46', '2026-03-06 03:09:22', NULL, 'failed');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_notes`
--
ALTER TABLE `admin_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `date` (`date`),
  ADD KEY `attendance_shift_fk` (`shift_id`);

--
-- Indexes for table `attendance_summary`
--
ALTER TABLE `attendance_summary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_period` (`employee_id`,`period_start`,`period_end`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `period_dates` (`period_start`,`period_end`);

--
-- Indexes for table `benefit_providers`
--
ALTER TABLE `benefit_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provider_name` (`provider_name`);

--
-- Indexes for table `compensation_reviews`
--
ALTER TABLE `compensation_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `status` (`status`),
  ADD KEY `effective_date` (`effective_date`);

--
-- Indexes for table `competencies`
--
ALTER TABLE `competencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `competency_assessments`
--
ALTER TABLE `competency_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_assessment_employee` (`employee_id`),
  ADD KEY `fk_assessment_competency` (`competency_id`),
  ADD KEY `fk_assessment_assessor` (`assessor_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_number` (`employee_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `applicant_id` (`applicant_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indexes for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_applicant_id` (`applicant_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_username` (`username`);

--
-- Indexes for table `employee_benefits`
--
ALTER TABLE `employee_benefits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `employee_recognitions`
--
ALTER TABLE `employee_recognitions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_schedules`
--
ALTER TABLE `employee_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `schedule_date` (`schedule_date`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indexes for table `expense_claims`
--
ALTER TABLE `expense_claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_expense_date` (`expense_date`),
  ADD KEY `fk_claim_approver` (`approved_by`);

--
-- Indexes for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_job_posting` (`position`,`department`,`location`,`shift`,`salary`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `mentor_assignments`
--
ALTER TABLE `mentor_assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mentor_ratings`
--
ALTER TABLE `mentor_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rating_mentee` (`mentee_employee_id`),
  ADD KEY `fk_rating_mentor` (`mentor_employee_id`);

--
-- Indexes for table `payroll_summary`
--
ALTER TABLE `payroll_summary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_pay_period` (`employee_id`,`period_start`,`period_end`);

--
-- Indexes for table `performance_criteria_scores`
--
ALTER TABLE `performance_criteria_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evaluation_id` (`evaluation_id`);

--
-- Indexes for table `performance_evaluations`
--
ALTER TABLE `performance_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `performance_improvement_plans`
--
ALTER TABLE `performance_improvement_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `evaluation_id` (`evaluation_id`);

--
-- Indexes for table `regular_employment`
--
ALTER TABLE `regular_employment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `evaluation_id` (`evaluation_id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shift_code` (`shift_code`);

--
-- Indexes for table `shift_swap_requests`
--
ALTER TABLE `shift_swap_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statutory_deductions`
--
ALTER TABLE `statutory_deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_ibfk_1` (`assigned_to`);

--
-- Indexes for table `training_providers`
--
ALTER TABLE `training_providers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_schedule`
--
ALTER TABLE `training_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employee` (`employee_id`),
  ADD KEY `fk_competency` (`competency_id`),
  ADD KEY `fk_provider` (`provider_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notes`
--
ALTER TABLE `admin_notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `attendance_summary`
--
ALTER TABLE `attendance_summary`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `benefit_providers`
--
ALTER TABLE `benefit_providers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `compensation_reviews`
--
ALTER TABLE `compensation_reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `competencies`
--
ALTER TABLE `competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `competency_assessments`
--
ALTER TABLE `competency_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `employee_benefits`
--
ALTER TABLE `employee_benefits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `employee_recognitions`
--
ALTER TABLE `employee_recognitions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_schedules`
--
ALTER TABLE `employee_schedules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `expense_claims`
--
ALTER TABLE `expense_claims`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `job_postings`
--
ALTER TABLE `job_postings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `mentor_assignments`
--
ALTER TABLE `mentor_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mentor_ratings`
--
ALTER TABLE `mentor_ratings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payroll_summary`
--
ALTER TABLE `payroll_summary`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performance_criteria_scores`
--
ALTER TABLE `performance_criteria_scores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `performance_evaluations`
--
ALTER TABLE `performance_evaluations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `performance_improvement_plans`
--
ALTER TABLE `performance_improvement_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `regular_employment`
--
ALTER TABLE `regular_employment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `shift_swap_requests`
--
ALTER TABLE `shift_swap_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `statutory_deductions`
--
ALTER TABLE `statutory_deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `training_providers`
--
ALTER TABLE `training_providers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `training_schedule`
--
ALTER TABLE `training_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_notes`
--
ALTER TABLE `admin_notes`
  ADD CONSTRAINT `fk_admin_notes_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_shift_fk` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `compensation_reviews`
--
ALTER TABLE `compensation_reviews`
  ADD CONSTRAINT `fk_compensation_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `competency_assessments`
--
ALTER TABLE `competency_assessments`
  ADD CONSTRAINT `fk_assessment_assessor` FOREIGN KEY (`assessor_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_assessment_competency` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_assessment_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD CONSTRAINT `employee_accounts_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_benefits`
--
ALTER TABLE `employee_benefits`
  ADD CONSTRAINT `employee_benefits_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_benefits_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `benefit_providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_schedules`
--
ALTER TABLE `employee_schedules`
  ADD CONSTRAINT `fk_schedule_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_schedule_shift` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expense_claims`
--
ALTER TABLE `expense_claims`
  ADD CONSTRAINT `fk_claim_approver` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_claim_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentor_ratings`
--
ALTER TABLE `mentor_ratings`
  ADD CONSTRAINT `fk_rating_mentee` FOREIGN KEY (`mentee_employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `fk_rating_mentor` FOREIGN KEY (`mentor_employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `payroll_summary`
--
ALTER TABLE `payroll_summary`
  ADD CONSTRAINT `payroll_summary_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `performance_criteria_scores`
--
ALTER TABLE `performance_criteria_scores`
  ADD CONSTRAINT `criteria_scores_ibfk_1` FOREIGN KEY (`evaluation_id`) REFERENCES `performance_evaluations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `performance_evaluations`
--
ALTER TABLE `performance_evaluations`
  ADD CONSTRAINT `performance_evaluations_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `performance_improvement_plans`
--
ALTER TABLE `performance_improvement_plans`
  ADD CONSTRAINT `pip_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pip_ibfk_2` FOREIGN KEY (`evaluation_id`) REFERENCES `performance_evaluations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `regular_employment`
--
ALTER TABLE `regular_employment`
  ADD CONSTRAINT `regular_employment_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `regular_employment_ibfk_2` FOREIGN KEY (`evaluation_id`) REFERENCES `performance_evaluations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training_schedule`
--
ALTER TABLE `training_schedule`
  ADD CONSTRAINT `fk_competency` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_provider` FOREIGN KEY (`provider_id`) REFERENCES `training_providers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
