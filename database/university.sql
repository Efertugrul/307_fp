-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 06 Ara 2024, 20:02:06
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `courses` (
  `course_code` varchar(50) NOT NULL AUTO_INCREMENT,
  `course_name` varchar(255) NOT NULL,
  `course_description` text DEFAULT NULL,
  PRIMARY KEY (`course_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `sections` (
  `section_code` int NOT NULL AUTO_INCREMENT,
  `semester` varchar(11) NOT NULL,
  `professor` varchar(100) DEFAULT NULL,
  `course_code` varchar(50) NOT NULL,
  PRIMARY KEY (`section_code`),
  CONSTRAINT `fk_course_code` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `lectures` (
  `lecture_id` int NOT NULL AUTO_INCREMENT,
  `location` varchar(100) DEFAULT NULL,
  `day_of_week` enum('Mon','Tue','Wed','Thu','Fri') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `section_code` int NOT NULL,
  PRIMARY KEY (`lecture_id`),
  CONSTRAINT `fk_section_code` FOREIGN KEY (`section_code`) REFERENCES `sections` (`section_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `students` (
  `student_id` int NOT NULL AUTO_INCREMENT,
  `fname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `coursesEnrolled` (
  `student_id` int NOT NULL,
  `section_code` int NOT NULL,
  PRIMARY KEY (`student_id`, `section_code`),
  CONSTRAINT `fk_enrolled_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_enrolled_section` FOREIGN KEY (`section_code`) REFERENCES `sections` (`section_code`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `coursesCompleted` (
  `student_id` int NOT NULL,
  `course_code` varchar(50) NOT NULL,
  PRIMARY KEY (`student_id`, `course_code`),
  CONSTRAINT `fk_completed_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_completed_course` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `FriendsWith` (
  `student_id1` int NOT NULL,
  `student_id2` int NOT NULL,
  PRIMARY KEY (`student_id1`, `student_id2`),
  CONSTRAINT `fk_friend1` FOREIGN KEY (`student_id1`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_friend2` FOREIGN KEY (`student_id2`) REFERENCES `students` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `PrerequisiteOf` (
  `course_code` varchar(50) NOT NULL,
  `prerequisite_course_code` varchar(50) NOT NULL,
  PRIMARY KEY (`course_code`, `prerequisite_course_code`),
  CONSTRAINT `fk_prerequisite_course` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE,
  CONSTRAINT `fk_prerequisite` FOREIGN KEY (`prerequisite_course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



INSERT INTO `courses` (`course_code`, `course_name`, `course_description`) VALUES
('BIOL101', 'Introduction to Biology', NULL),
('BIOL202', 'Cell Biology', NULL),
('BIOL303', 'Genetics', NULL),
('BIOL404', 'Marine Biology', NULL),
('BIOL505', 'Molecular Biology', NULL),
('BIOL606', 'Human Anatomy', NULL),
('BIOL707', 'Immunology', NULL),
('BIOL808', 'Neuroscience', NULL),
('BIOL909', 'Ecology and Evolution', NULL),
('BIOL999', 'Advanced Botany', NULL);


INSERT INTO `sections` (`section_code`, `semester`, `professor`, `course_code`) VALUES
(4819, 'F24', 'Peter Johnson', 'BIOL101'),
(2057, 'F24', 'John Green', 'BIOL202'),
(3220, 'W25', 'Michael Rodriguez', 'BIOL303'),
(8301, 'W25', 'Don Miller', 'BIOL404');


INSERT INTO `lectures` (`lecture_id`, `location`, `day_of_week`, `start_time`, `end_time`, `section_code`) VALUES
(9872, 'Room A', 'Mon', '09:00:00', '10:00:00', 4819),
(5084, 'Room B', 'Wed', '09:00:00', '10:00:00', 2057),
(1024, 'Room C', 'Tue', '13:00:00', '14:30:00', 3220),
(4895, 'Room D', 'Fri', '10:00:00', '12:00:00', 8301);


INSERT INTO `students` (`student_id`, `fname`, `lname`, `email`, `password_hash`, `remember_token`) VALUES
(1, 'John', 'Wood', 'alice.smith@university.edu', 'Efstarisback', '$2y$10$QUvS4QATistNhdFM2qTf7u2cUfphR9LGbowI8Y2ZIf8rrKvBeM9C6', NULL),
(2, 'Jim', 'King', 'bob.johnson@university.edu', NULL, NULL, NULL),
(3, 'Tony', 'Cole', 'charlie.brown@university.edu', 'Efert', '$2y$10$AJPLF204rYwoLfsFsfbMTuOw6kXkCAbJu7ZYMvDqabQlRAbWDVGfK', NULL),
(4, 'Mike', 'Reed', 'diana.prince@university.edu', NULL, NULL, NULL),
(5, 'David', 'West', 'edward.norton@university.edu', NULL, NULL, NULL),
(6, 'Tim', 'Page', 'fiona.apple@university.edu', NULL, NULL, NULL),
(7, 'Frank', 'Snow', 'george.clooney@university.edu', NULL, NULL, NULL),
(8, 'Ben', 'Gray', 'hannah.montana@university.edu', NULL, NULL, NULL),
(9, 'Mark', 'Lane', 'ian.mckellen@university.edu', NULL, NULL, NULL),
(10, 'Paul', 'Ford', 'julia.roberts@university.edu', NULL, NULL, NULL);


INSERT INTO `coursesEnrolled` (`student_id`, `section_code`) VALUES
(1, 4819),
(1, 2057),
(2, 3220),
(2, 8301),
(3, 2057),
(4, 4819),
(5, 3220),
(6, 8301),
(7, 2057),
(8, 4819),
(9, 3220),
(10, 8301);


INSERT INTO `coursesCompleted` (`student_id`, `course_code`) VALUES
(1, 'BIOL101'),
(1, 'BIOL202'),
(2, 'BIOL101'),
(3, 'BIOL101'),
(3, 'BIOL202'),
(4, 'BIOL101'),
(5, 'BIOL101'),
(6, 'BIOL202'),
(7, 'BIOL101'),
(8, 'BIOL202'),
(9, 'BIOL101'),
(10, 'BIOL101');


INSERT INTO `FriendsWith` (`student_id1`, `student_id2`) VALUES
(1, 2),
(1, 3),
(2, 4),
(3, 5),
(4, 6),
(5, 7),
(6, 8),
(7, 9),
(8, 10),
(2, 5),
(3, 6);


INSERT INTO `PrerequisiteOf` (`course_code`, `prerequisite_course_code`) VALUES
('BIOL202', 'BIOL101'),
('BIOL303', 'BIOL202'),
('BIOL404', 'BIOL303'),
('BIOL505', 'BIOL404'),
('BIOL606', 'BIOL505'),
('BIOL707', 'BIOL606'),
('BIOL808', 'BIOL707'),
('BIOL909', 'BIOL808'),
('BIOL999', 'BIOL909');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
