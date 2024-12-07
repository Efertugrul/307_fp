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
  `course_code` varchar(50) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_description` text DEFAULT NULL,
  PRIMARY KEY (`course_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `sections` (
  `section_code` int(11) NOT NULL,
  `semester` varchar(11) NOT NULL,
  `professor` varchar(100) DEFAULT NULL,
  `course_code` varchar(50) NOT NULL,
  PRIMARY KEY (`section_code`),
  CONSTRAINT `fk_course_code` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `lectures` (
  `lecture_id` int(11) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `day_of_week` enum('Mon','Tue','Wed','Thu','Fri') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `section_code` int(11) NOT NULL,
  PRIMARY KEY (`lecture_id`),
  CONSTRAINT `fk_section_code` FOREIGN KEY (`section_code`) REFERENCES `sections` (`section_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `fname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL
  PRIMARY KEY (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `coursesEnrolled` (
  `student_id` int(11) NOT NULL,
  `section_code` int(11) NOT NULL,
  PRIMARY KEY (`student_id`, `section_code`)
  CONSTRAINT `fk_enrolled_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_enrolled_section` FOREIGN KEY (`section_code`) REFERENCES `sections` (`section_code`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `coursesCompleted` (
  `student_id` int(11) NOT NULL,
  `course_code` varchar(50) NOT NULL,
  PRIMARY KEY (`student_id`, `course_code`),
  CONSTRAINT `fk_completed_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_completed_course` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `FriendsWith` (
  `student_id1` int(11) NOT NULL,
  `student_id2` int(11) NOT NULL,
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
(4819, 'F24', `Peter Johnson`, `BIOL101`),
(2057, 'F24', `John Green`,`BIOL202`),
(3220, 'W25', `Michael Rodriguez`,`BIOL303`),
(8301, 'W25', `Don Miller`,`BIOL404`);


INSERT INTO `lectures` (`lecture_id`, `day_of_week`, `semester_id`, `day_of_week`, `start_time`, `end_time`, `location`) VALUES
(1, 1, 1, 'Mon', '09:00:00', '10:00:00', 'Room A'),
(2, 1, 1, 'Wed', '09:00:00', '10:00:00', 'Room A'),
(3, 2, 1, 'Tue', '13:00:00', '14:30:00', 'Room B'),
(4, 3, 1, 'Fri', '10:00:00', '12:00:00', 'Room C');


INSERT INTO `users` (`id`, `student_id`, `email`, `username`, `password_hash`, `remember_token`) VALUES
(1, 'S1001', 'alice.smith@university.edu', 'Efstarisback', '$2y$10$QUvS4QATistNhdFM2qTf7u2cUfphR9LGbowI8Y2ZIf8rrKvBeM9C6', NULL),
(2, 'S1002', 'bob.johnson@university.edu', NULL, NULL, NULL),
(3, 'S1003', 'charlie.brown@university.edu', 'Efert', '$2y$10$AJPLF204rYwoLfsFsfbMTuOw6kXkCAbJu7ZYMvDqabQlRAbWDVGfK', NULL),
(4, 'S1004', 'diana.prince@university.edu', NULL, NULL, NULL),
(5, 'S1005', 'edward.norton@university.edu', NULL, NULL, NULL),
(6, 'S1006', 'fiona.apple@university.edu', NULL, NULL, NULL),
(7, 'S1007', 'george.clooney@university.edu', NULL, NULL, NULL),
(8, 'S1008', 'hannah.montana@university.edu', NULL, NULL, NULL),
(9, 'S1009', 'ian.mckellen@university.edu', NULL, NULL, NULL),
(10, 'S1010', 'julia.roberts@university.edu', NULL, NULL, NULL);


--
-- Tablo için AUTO_INCREMENT değeri `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Tablo için AUTO_INCREMENT değeri `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Tablo için AUTO_INCREMENT değeri `users_schedules`
--
ALTER TABLE `users_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sections_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `users_schedules`
--
ALTER TABLE `users_schedules`
  ADD CONSTRAINT `users_schedules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_schedules_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_schedules_ibfk_3` FOREIGN KEY (`course_offering_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
