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
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `badge` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'assets/doctor_1.jpg',
  `description` text DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT '923008053198',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data for `doctors`
INSERT INTO `doctors` (`id`, `name`, `role`, `badge`, `image`, `description`, `whatsapp`) VALUES
(1, 'Dr. Arthur Pendleton', 'Senior Medical Consultant', 'Internal Medicine', 'assets/doctor_1.jpg', 'Provides consultations for general health concerns, ongoing medical conditions and health needs of older adults.', '923008053198'),
(2, 'Dr. Sarah Jenkins', 'Medical Consultant', 'Home Care', 'assets/doctor_2.jpg', 'Provides consultations for routine check-ups, recovery after surgery and health concerns that can be managed at home.', '923008053198'),
(3, 'Dr. Hamza Tariq', 'Cardiology Consultant', 'Heart Care', 'assets/bp-check.jpg', 'Provides consultations for blood pressure, heart-related concerns, ECG review and follow-up care.', '923008053198'),
(4, 'Dr. Ayesha Malik', 'Physiotherapy Consultant', 'Rehabilitation', 'assets/home-care-facility.jpg', 'Provides rehabilitation support for recovery after stroke, joint movement and common mobility problems.', '923008053198');

-- --------------------------------------------------------
-- Table structure for table `staff`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `badge` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'assets/staff_1.jpg',
  `description` text DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT '923008053198',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data for `staff`
INSERT INTO `staff` (`id`, `name`, `role`, `badge`, `image`, `description`, `whatsapp`) VALUES
(1, 'James N.', 'Senior Registered Nurse', 'ICU Nurse', 'assets/staff_1.jpg', '8+ years experience in ICU nursing, ventilator care, tracheostomy support, and IV medication administration.', '923008053198'),
(2, 'Maria K.', 'Elderly Care Specialist', 'Senior Caregiver', 'assets/staff_2.jpg', 'Compassionate caregiver specializing in elderly assistance, dementia support, and daily personal care routines.', '923008053198'),
(3, 'Tariq Mahmood', 'Home Physiotherapist', 'Physiotherapist', 'assets/why-choose-us.jpg', 'Expert physical trainer for post-stroke mobility rehabilitation, muscle strengthening, and gait training.', '923008053198'),
(4, 'Fatima Zahra', 'Clinical Assistant', 'Patient Care Attendant', 'assets/who-we-are.jpg', 'Trained healthcare assistant providing round-the-clock bedside care, vitals tracking, and patient hygiene support.', '923008053198');

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
