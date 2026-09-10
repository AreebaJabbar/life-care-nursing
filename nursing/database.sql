-- ============================================================
-- LifeCare Nursing & Medical Services Database Export
-- Compatible with MySQL 5.7+ / MySQL 8.0 / MariaDB / phpMyAdmin
-- Database Name: lifecare_db
-- ============================================================

CREATE DATABASE IF NOT EXISTS `lifecare_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lifecare_db`;

-- --------------------------------------------------------
-- Table structure for table `doctors`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `doctors`;
CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'approved',
  `name` varchar(255) NOT NULL,
  `speciality` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `qualifications` varchar(255) DEFAULT NULL,
  `badge` varchar(100) DEFAULT 'PMC Verified',
  `experience` varchar(100) DEFAULT '10 Years',
  `waitTime` varchar(100) DEFAULT 'Under 15 Mins',
  `satisfaction` varchar(100) DEFAULT '97%',
  `hospitalName` varchar(255) DEFAULT 'LifeCare Clinical Center',
  `hospitalFee` varchar(100) DEFAULT '1500',
  `hospitalSchedule` varchar(255) DEFAULT 'Mon - Sat: 02:00 PM - 05:00 PM',
  `videoFee` varchar(100) DEFAULT '1000',
  `videoSchedule` varchar(255) DEFAULT 'Mon - Sun: 09:00 AM - 04:00 PM',
  `image` varchar(255) NOT NULL DEFAULT 'assets/doctor_1.jpg',
  `description` text DEFAULT NULL,
  `aboutBio` text DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT '923008053198',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data for `doctors`
INSERT INTO `doctors` (`id`, `username`, `password`, `status`, `name`, `role`, `speciality`, `badge`, `image`, `description`, `whatsapp`) VALUES
(1, 'doctor1', '$2y$10$8v5p.aO8H3Llh9L8dGZ.eu1Dk0.x3J8Xb8iF0JpM5c4c9j7PqL2OS', 'approved', 'Dr. Arthur Pendleton', 'Senior Medical Consultant', 'Internal Medicine', 'Internal Medicine', 'assets/doctor_1.jpg', 'Provides consultations for general health concerns, ongoing medical conditions and health needs of older adults.', '923008053198'),
(2, 'doctor2', '$2y$10$8v5p.aO8H3Llh9L8dGZ.eu1Dk0.x3J8Xb8iF0JpM5c4c9j7PqL2OS', 'approved', 'Dr. Sarah Jenkins', 'Medical Consultant', 'Home Care', 'Home Care', 'assets/doctor_2.jpg', 'Provides consultations for routine check-ups, recovery after surgery and health concerns that can be managed at home.', '923008053198'),
(3, 'doctor3', '$2y$10$8v5p.aO8H3Llh9L8dGZ.eu1Dk0.x3J8Xb8iF0JpM5c4c9j7PqL2OS', 'approved', 'Dr. Hamza Tariq', 'Cardiology Consultant', 'Cardiology', 'Heart Care', 'assets/bp-check.jpg', 'Provides consultations for blood pressure, heart-related concerns, ECG review and follow-up care.', '923008053198'),
(4, 'doctor4', '$2y$10$8v5p.aO8H3Llh9L8dGZ.eu1Dk0.x3J8Xb8iF0JpM5c4c9j7PqL2OS', 'approved', 'Dr. Ayesha Malik', 'Physiotherapy Consultant', 'Physiotherapy', 'Rehabilitation', 'assets/home-care-facility.jpg', 'Provides rehabilitation support for recovery after stroke, joint movement and common mobility problems.', '923008053198');

-- --------------------------------------------------------
-- Table structure for table `staff`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'approved',
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT 'Faisalabad',
  `badge` varchar(100) NOT NULL,
  `shift` varchar(100) DEFAULT '12-Hour Shift',
  `rate` varchar(100) DEFAULT 'Rs. 2,200 / Day',
  `skills` text DEFAULT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'assets/staff_1.jpg',
  `description` text DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT '923008053198',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data for `staff`
INSERT INTO `staff` (`id`, `username`, `password`, `status`, `name`, `role`, `city`, `badge`, `shift`, `rate`, `skills`, `image`, `description`, `whatsapp`) VALUES
(1, 'staff1', '$2y$10$8v5p.aO8H3Llh9L8dGZ.eu1Dk0.x3J8Xb8iF0JpM5c4c9j7PqL2OS', 'approved', 'James N.', 'Senior Registered Nurse', 'Faisalabad', 'ICU Nurse', '12-Hour Shift', 'Rs. 3,000 / Day', 'ICU Nursing,Ventilator Care,IV Medication', 'assets/staff_1.jpg', '8+ years experience in ICU nursing, ventilator care, tracheostomy support, and IV medication administration.', '923008053198'),
(2, 'staff2', '$2y$10$8v5p.aO8H3Llh9L8dGZ.eu1Dk0.x3J8Xb8iF0JpM5c4c9j7PqL2OS', 'approved', 'Maria K.', 'Elderly Care Specialist', 'Faisalabad', 'Senior Caregiver', '12-Hour Shift', 'Rs. 2,200 / Day', 'Elderly Care,Dementia Support,Personal Care', 'assets/staff_2.jpg', 'Compassionate caregiver specializing in elderly assistance, dementia support, and daily personal care routines.', '923008053198'),
(3, 'staff3', '$2y$10$8v5p.aO8H3Llh9L8dGZ.eu1Dk0.x3J8Xb8iF0JpM5c4c9j7PqL2OS', 'approved', 'Tariq Mahmood', 'Home Physiotherapist', 'Lahore', 'Physiotherapist', 'Per Session', 'Rs. 2,500 / Session', 'Post-Stroke Rehab,Muscle Strengthening,Gait Training', 'assets/why-choose-us.jpg', 'Expert physical trainer for post-stroke mobility rehabilitation, muscle strengthening, and gait training.', '923008053198'),
(4, 'staff4', '$2y$10$8v5p.aO8H3Llh9L8dGZ.eu1Dk0.x3J8Xb8iF0JpM5c4c9j7PqL2OS', 'approved', 'Fatima Zahra', 'Clinical Assistant', 'Islamabad', 'Patient Care Attendant', '24-Hour Shift', 'Rs. 2,000 / Day', 'Bedside Care,Vitals Tracking,Hygiene Support', 'assets/who-we-are.jpg', 'Trained healthcare assistant providing round-the-clock bedside care, vitals tracking, and patient hygiene support.', '923008053198');

-- --------------------------------------------------------
-- Table structure for table `team`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `badge` varchar(100) DEFAULT 'Management',
  `image` varchar(255) NOT NULL DEFAULT 'assets/doctor_1.jpg',
  `description` text DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT '923008053198',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data for `team`
INSERT INTO `team` (`id`, `name`, `role`, `badge`, `image`, `description`, `whatsapp`) VALUES
(1, 'Dr. Haris Abbasi', 'Medical Director', 'Management', 'assets/doctor_1.jpg', 'Oversees clinical operations, patient care quality, and doctor panel coordination.', '923008053198'),
(2, 'Zainab Bibi', 'Nursing Superintendent', 'Staff Lead', 'assets/staff_2.jpg', 'Head of nursing services, staff assignment, and emergency home care deployments.', '923008053198');

-- --------------------------------------------------------
-- Table structure for table `contact_messages`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `service` varchar(255) DEFAULT 'General Inquiry',
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `admins`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT 'lifecarenursing5@gmail.com',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Admin User (username: admin / password: password hash for admin123)
INSERT INTO `admins` (`id`, `username`, `password`, `email`) VALUES
(1, 'admin', '$2y$10$8v5p.aO8H3Llh9L8dGZ.eu1Dk0.x3J8Xb8iF0JpM5c4c9j7PqL2OS', 'lifecarenursing5@gmail.com');
