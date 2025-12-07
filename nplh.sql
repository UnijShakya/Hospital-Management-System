-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 07:16 PM
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
-- Database: `nplh`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us`
--

CREATE TABLE `about_us` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_us`
--

INSERT INTO `about_us` (`id`, `title`, `description`, `image`, `created_at`) VALUES
(4, 'About US', 'Welcome to Nepal Hosital where compassionate care meets advanced medical excellence. Established in 2025 , our hospital has been dedicated to providing comprehensive healthcare services to the community with a focus on patient well-being, safety, and innovation.\r\n\r\nOur team of highly skilled doctors, nurses, and medical professionals is committed to delivering personalized treatment across a wide range of specialties, including list key departments, e.g., cardiology, orthopedics, pediatrics, oncology. We combine state-of-the-art technology with a patient-centered approach to ensure accurate diagnoses, effective treatments, and faster recovery.\r\n\r\nAt Nepal Hospital we believe that healthcare goes beyond medicine. We prioritize patient comfort, family support, and health education to empower our patients to lead healthier lives. Our modern facilities, 24/7 emergency services, and dedicated support staff make us a trusted choice for healthcare in Nepal Kathmandu.\r\n\r\nMission: To provide accessible, high-quality healthcare with compassion and integrity.\r\nVision: To be a leading healthcare institution recognized for clinical excellence, innovation, and patient satisfaction.', 'uploads/about/1758383827_why.jpg', '2025-09-20 15:57:07');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `contact`, `password_hash`, `created_at`, `reset_token`, `reset_expires`) VALUES
(1, 'admin', 'admin@gmail.com', '9865466744', '$2y$10$jB3bAhfKLN0OMtVT2aqbe.cWr7yBu/vU2M9Y.Kuys5eJKchqtwFFq', '2025-09-23 14:11:29', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_slots`
--

CREATE TABLE `appointment_slots` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('available','booked') DEFAULT 'available',
  `patient_id` int(11) DEFAULT NULL,
  `patient_name` varchar(100) DEFAULT NULL,
  `patient_contact` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_slots`
--

INSERT INTO `appointment_slots` (`id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`, `patient_id`, `patient_name`, `patient_contact`) VALUES
(42, 1, '2025-10-30', '10:00:00', 'booked', 1, NULL, NULL),
(43, 1, '2025-10-30', '10:30:00', 'available', NULL, NULL, NULL),
(44, 1, '2025-10-30', '11:00:00', 'available', NULL, NULL, NULL),
(45, 1, '2025-10-30', '11:30:00', 'available', NULL, NULL, NULL),
(46, 1, '2025-10-30', '12:00:00', 'available', NULL, NULL, NULL),
(47, 1, '2025-10-30', '12:30:00', 'available', NULL, NULL, NULL),
(48, 1, '2025-10-30', '13:30:00', 'available', NULL, NULL, NULL),
(49, 1, '2025-10-30', '14:00:00', 'available', NULL, NULL, NULL),
(50, 1, '2025-10-30', '14:30:00', 'available', NULL, NULL, NULL),
(51, 1, '2025-10-30', '15:00:00', 'available', NULL, NULL, NULL),
(52, 1, '2025-10-30', '15:30:00', 'available', NULL, NULL, NULL),
(53, 1, '2025-10-30', '16:00:00', 'available', NULL, NULL, NULL),
(54, 1, '2025-10-30', '16:30:00', 'available', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `image`, `uploaded_at`) VALUES
(1, 'uploads/1758379424_slider_1.jpg', '2025-09-20 14:43:44'),
(2, 'uploads/1758379442_slider_2.jpg', '2025-09-20 14:44:02');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `admin_reply` text DEFAULT NULL,
  `status` enum('pending','replied') DEFAULT 'pending',
  `token` varchar(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `admin_reply`, `status`, `token`, `created_at`) VALUES
(1, 'unij', 'unijshakya29@gmail.com', 'cardilofy', 'ihfuhsdufhui', 'goofd', 'replied', '24c6580413192cbd9add5af0fd326aa9', '2025-09-23 16:44:17'),
(2, 'unij', 'unijshakya29@gmail.com', 'cardilofy', 'bibbbbbbb', 'aaaaa', 'replied', '66c45bb6ce27a31970bd7013e4dd755a', '2025-09-23 16:45:08'),
(3, 'biswa', 'dzuse218@gmail.com', 'besh', 'desh sakio', NULL, 'pending', '0880f6a5e708be605b72ace6945d0435', '2025-09-23 16:47:58'),
(4, 'unij', 'unijshakya29@gmail.com', 'cardilofy', '1234567', NULL, 'pending', 'e03f28ccfaa3f0942da028f485051321', '2025-09-23 17:25:00'),
(5, 'biswa', 'dzuse218@gmail.com', 'cardilofy', 'dfuefuhe', 'oihfwhef', 'replied', '89efc4898895ac1cf47ea05e4dbdb03c', '2025-09-23 17:34:08'),
(6, 'biswa', 'dzuse218@gmail.com', 'cardilofy', 'fuew8fewibfu', NULL, 'pending', 'a9bb50a1b79a4b1db056f6a2c246ec21', '2025-09-23 17:40:58'),
(7, 'biswa', 'dzuse218@gmail.com', 'cardilofy', 'fuew8fewibfu', 'duihdifn', 'replied', '44f90ca71d0c968a39d236add3c78378', '2025-09-23 17:48:49'),
(8, 'biswa', 'dzuse218@gmail.com', 'cardilofy', 'fuew8fewibfu', 'abcdejihid', 'replied', 'da62a48f4faab9aa7b89bdfbbdc255cd', '2025-09-23 17:55:45'),
(9, 'biswa', 'dzuse218@gmail.com', 'cardilofy', 'fuew8fewibfu', 'diiahsidf', 'replied', 'f5fc5c5cb936dd8bbc922b64b38a3a66', '2025-09-23 17:56:25'),
(10, 'biswa', 'dzuse218@gmail.com', 'cardilofy', 'fuew8fewibfu', 'abcde', 'replied', 'de712afaa7af5ae6b3935773f98b0ac9', '2025-09-23 17:56:47'),
(11, 'biswa', 'dzuse218@gmail.com', 'cardilofy', 'fuew8fewibfu', 'fjiodfjn', 'replied', 'f1ed7713f046977b47fa1b816a27ddf2', '2025-09-23 17:57:45'),
(12, 'momo', 'unijshakya29@gmail.com', 'card', 'sdfadf', NULL, 'pending', '66c51553487e80a136f098b3ab35268c', '2025-10-15 17:49:44');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `specialization_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `doctor_name` varchar(150) NOT NULL,
  `clinic_address` varchar(255) NOT NULL,
  `consultancy_fees` decimal(10,2) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `specialization_id`, `department_id`, `doctor_name`, `clinic_address`, `consultancy_fees`, `contact_no`, `email`, `password`, `created_at`, `reset_token`, `reset_expiry`) VALUES
(1, 1, 0, 'Unij', '11', 1500.00, '9865466744', 'unijshakya29@gmail.com', '$2y$10$sdcAaPrwolf8FOFAUjv2POPoRmptGAMv6uHCv4o9xrvLYHd0xd.E.', '2025-09-24 06:34:44', NULL, NULL),
(2, 3, 0, 'Swoyan', '12', 2500.00, '9767472474', 'swoyanbajra18@gmail.com', '$2y$10$Iv0vMwNpSJagoXqnAOb6/.iAhaWpHAqlGqX/C4JNh0i9c0BB0ASc6', '2025-10-30 16:08:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_breaks`
--

CREATE TABLE `doctor_breaks` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `break_date` date NOT NULL,
  `break_start` time NOT NULL,
  `break_end` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_breaks`
--

INSERT INTO `doctor_breaks` (`id`, `doctor_id`, `break_date`, `break_start`, `break_end`, `created_at`, `updated_at`) VALUES
(4, 1, '2025-10-30', '13:00:00', '13:30:00', '2025-10-30 16:04:16', '2025-10-30 16:04:16');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_specialization`
--

CREATE TABLE `doctor_specialization` (
  `id` int(11) NOT NULL,
  `specialization_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_specialization`
--

INSERT INTO `doctor_specialization` (`id`, `specialization_name`) VALUES
(1, 'Cardilogy'),
(3, 'Neurology');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `cat_id`, `image`, `uploaded_at`) VALUES
(2, 1, '68d3192bae130.jpg', '2025-09-23 22:03:23'),
(3, 2, '68d319776e24a.jpg', '2025-09-23 22:04:39');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_categories`
--

CREATE TABLE `gallery_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_categories`
--

INSERT INTO `gallery_categories` (`id`, `title`, `created_at`) VALUES
(1, 'Cardilogy', '2025-09-23 21:28:50'),
(2, 'Neurology', '2025-09-23 22:04:39');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `patient_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT 'Male',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `doctor_id`, `patient_name`, `email`, `password`, `contact_no`, `age`, `gender`, `reset_token`, `reset_expiry`, `created_at`) VALUES
(1, NULL, 'Bipin', 'bipinshakya46@gmail.com', '$2y$10$lDbI0SpmjYCllIyzLQ1P9uKuXu5a4p1c01IH9MSbr4cM5TZcHVhtK', '9861391074', 28, 'Male', NULL, NULL, '2025-09-24 09:00:48');

-- --------------------------------------------------------

--
-- Table structure for table `patient_reports`
--

CREATE TABLE `patient_reports` (
  `id` int(11) NOT NULL,
  `report_date` date NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_name` varchar(150) NOT NULL,
  `age` int(3) DEFAULT NULL,
  `DOB` date NOT NULL,
  `gender` varchar(255) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `doctor_name` varchar(150) NOT NULL,
  `doctor_contact` varchar(20) DEFAULT NULL,
  `report_title` varchar(255) NOT NULL,
  `report_description` text DEFAULT NULL,
  `medicine_prescription` text DEFAULT NULL,
  `report_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_reports`
--

INSERT INTO `patient_reports` (`id`, `report_date`, `patient_id`, `doctor_id`, `patient_name`, `age`, `DOB`, `gender`, `contact`, `address`, `blood_group`, `doctor_name`, `doctor_contact`, `report_title`, `report_description`, `medicine_prescription`, `report_file`, `created_at`) VALUES
(9, '2025-10-22', 1, 1, 'Bipin', 0, '0000-00-00', '', '9861391074', 'kirtipur', 'O+', 'Unij', '9865466744', 'Cardilogy', '<p>The patient presented with complaints of chest tightness and shortness of breath. Echocardiography revealed mild left ventricular hypertrophy and normal ejection fraction (EF 60%). ECG showed sinus rhythm with occasional premature ventricular contractions. No evidence of acute ischemia noted.</p><p><strong>Findings:</strong></p><ul><li>Left ventricle: Mild concentric hypertrophy</li><li>Right ventricle: Normal function</li><li>Valves: No significant regurgitation or stenosis</li><li>Pericardium: Normal</li><li>ECG: Sinus rhythm, occasional PVCs</li></ul><p><strong>Diagnosis:</strong></p><ul><li>Mild Left Ventricular Hypertrophy</li><li>Borderline Hypertension</li><li>Dyslipidemia</li></ul>', '<ol><li>Metoprolol 50 mg1 tablet Twice a day30 days</li><li>Atorvastatin 20 mg1 tablet Once at night 30 days</li><li>Aspirin 75 mg1 tablet Once daily 30 days</li><li>Pantoprazole 40 mg1 tablet Before breakfast 15 days</li></ol>', NULL, '2025-10-22 17:52:45');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Cardiology', 'The Cardiology Department at Nepal Hospital specializes in the diagnosis, treatment, and prevention of heart and blood vessel disorders. Our team of experienced cardiologists and cardiac specialists provides comprehensive care for patients with conditions such as heart disease, arrhythmias, hypertension, heart failure, and congenital heart defects.\r\n\r\nWe combine advanced diagnostic tools like ECG, echocardiography, stress tests, and cardiac catheterization with modern treatment options, including medications, interventional procedures, and lifestyle management programs.\r\n\r\nOur goal is to ensure the heart health of our patients through personalized care, patient education, and preventive strategies. Whether it’s routine check-ups, emergency care, or complex cardiac procedures, we are dedicated to keeping your heart healthy and strong.\r\n\r\nServices Offered:\r\n\r\nHeart disease diagnosis & treatment\r\n\r\nCardiac imaging & echocardiography\r\n\r\nInterventional cardiology (angioplasty, stenting)\r\n\r\nArrhythmia management\r\n\r\nPreventive cardiology & lifestyle counseling\r\n\r\nRehabilitation after heart surgery or heart attack\r\n\r\nMission: To provide world-class cardiac care with compassion and precision.', '2025-09-20 16:33:04'),
(2, 'Neurology', 'Our hospital services include diagnosing and treating disorders of the brain, spinal cord, and nerves, with common services ranging from managing conditions like epilepsy and stroke to more specific care for movement disorders, headaches, and sleep issues', '2025-10-18 06:45:32'),
(3, 'Ophthalmology', 'This department provides comprehensive medical and surgical care for the eyes and visual system. \r\nGeneral eye care: Routine eye examinations to check for vision problems and eye diseases.\r\nRefractive services: Management of refractive errors, such as nearsightedness, with eyeglasses and contact lenses.\r\nCataract surgery: Advanced surgical techniques, including phacoemulsification, to remove cataracts and restore vision.\r\nRetina services: Diagnosis and treatment of retinal disorders, such as diabetic retinopathy and macular degeneration.', '2025-10-18 06:48:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_us`
--
ALTER TABLE `about_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `contact` (`contact`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `appointment_slots`
--
ALTER TABLE `appointment_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `specialization_id` (`specialization_id`);

--
-- Indexes for table `doctor_breaks`
--
ALTER TABLE `doctor_breaks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctor_specialization`
--
ALTER TABLE `doctor_specialization`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `patient_reports`
--
ALTER TABLE `patient_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_us`
--
ALTER TABLE `about_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointment_slots`
--
ALTER TABLE `appointment_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doctor_breaks`
--
ALTER TABLE `doctor_breaks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `doctor_specialization`
--
ALTER TABLE `doctor_specialization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `patient_reports`
--
ALTER TABLE `patient_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointment_slots`
--
ALTER TABLE `appointment_slots`
  ADD CONSTRAINT `appointment_slots_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_slots_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`specialization_id`) REFERENCES `doctor_specialization` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_breaks`
--
ALTER TABLE `doctor_breaks`
  ADD CONSTRAINT `doctor_breaks_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES `gallery_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_reports`
--
ALTER TABLE `patient_reports`
  ADD CONSTRAINT `patient_reports_ibfk_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_reports_ibfk_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
