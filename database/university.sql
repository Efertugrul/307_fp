-- coded by: all team members

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 12 Ara 2024, 21:49:07
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `degrees` (
  `degree_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('Major','Minor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `degrees` (`degree_id`, `name`, `type`) VALUES
(1,  'Computer Science',  'Major'),
(2,  'Computer Science',  'Minor'),
(3,  'Biology',           'Major'),
(4,  'Biology',           'Minor'),
(5,  'Political Science', 'Major'),
(6,  'Political Science', 'Minor'),
(7,  'Economics',         'Major'),
(8,  'Economics',         'Minor'),
(9,  'Philosophy',        'Major'),
(10, 'Philosophy',        'Minor'),
(11, 'English',           'Major'),
(12, 'English',           'Minor');


CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(100) DEFAULT NULL,
  `password_reset_code` varchar(64) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `password_change_code` varchar(6) DEFAULT NULL,
  `major_id` int(11) NOT NULL,
  `minor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `students` (`student_id`, `username`, `fname`, `lname`, `email`, `password_hash`, `remember_token`, `is_verified`, `verification_code`, `password_reset_code`, `password_reset_expires`, `password_change_code`, `major_id`, `minor_id`) VALUES
(1, 'jwood1', 'John', 'Wood', 'alice.smith@university.edu', '$2y$10$QUvS4QATistNhdFM2qTf7u2cUfphR9LGbowI8Y2ZIf8rrKvBeM9C6', NULL, 0, NULL, NULL, NULL, NULL, 1, NULL),
(2, 'eertugrul2', 'Efe', 'Ertugrul', 'efertugrul6@gmail.com', '$2y$10$gv9ASeGqmEmrDPF5omkJwOOpkPsMBY4Gmmr4k8IfWb64S.GhZn6xO', NULL, 1, NULL, NULL, NULL, '651008', 1, NULL),
(3, 'mreed3', 'Mike', 'Reed', 'diana.prince@university.edu', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL),
(4, 'dwest4', 'David', 'West', 'edward.norton@university.edu', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL),
(5, 'tpage5', 'Tim', 'Page', 'timoxa.gal@gmail.com', '$2y$10$MS0jyLdIHDcy.U3PDi3kOuqXjKwxgKUArBfBa2t3dswC2GopSdo4G', NULL, 1, NULL, NULL, NULL, NULL, 1, 4),
(6, 'fsnow6', 'Frank', 'Snow', 'george.clooney@university.edu', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL),
(7, 'bgray7', 'Ben', 'Gray', 'hannah.montana@university.edu', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL),
(8, 'mlane8', 'Mark', 'Lane', 'ian.mckellen@university.edu', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL),
(9, 'pford9', 'Paul', 'Ford', 'julia.roberts@university.edu', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL),
(10, 'Efstarisback2', NULL, NULL, 'ali.ertugrul@mail.mcgill.ca', '$2y$10$HDE78iT/SwIM7dCR3huFGeID9t/bQ.txDxOpSO7SX7zcC6YBIypjm', NULL, 1, NULL, '208887', '2024-12-12 22:44:17', NULL, 3, NULL);


CREATE TABLE `courses` (
  `course_code` varchar(50) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `courses` (`course_code`, `course_name`, `course_description`) VALUES
('BIOL-101', 'Introduction to Biology', 'Explore fundamental biological concepts, including cell theory, genetics, and evolution.'),
('BIOL-102', 'Cellular Biology', 'Dive into the structure and function of cells, organelles, and molecular mechanisms.'),
('BIOL-201', 'Genetics', 'Study the principles of inheritance, DNA structure, and genetic disorders.'),
('BIOL-202', 'Ecology Basics', 'Understand the interactions between organisms and their environments, focusing on ecosystems and biodiversity.'),
('BIOL-301', 'Human Physiology', 'Examine the functioning of human body systems and their roles in maintaining health.'),
('BIOL-302', 'Microbiology', 'Study the characteristics, behaviors, and ecological roles of microorganisms.'),
('COMP-101', 'Introduction to Programming', 'Learn foundational programming concepts, develop algorithms, and create simple computer applications.'),
('COMP-102', 'Foundations of Computing', 'Understand computer systems, digital logic, and their role in modern technologies.'),
('COMP-201', 'Data Structures', 'Examine efficient data storage and retrieval structures essential for effective programming.'),
('COMP-202', 'Web Development Basics', 'Discover how to create interactive websites using HTML, CSS, and JavaScript.'),
('COMP-301', 'Artificial Intelligence Fundamentals', 'Explore AI concepts, algorithms, and their applications in real-world scenarios.'),
('COMP-302', 'Operating Systems', 'Study operating systems principles, including memory management, process scheduling, and file systems.'),
('ECON-101', 'Principles of Microeconomics', 'Study decision-making processes within households, firms, and resource allocation systems.'),
('ECON-102', 'Principles of Macroeconomics', 'Understand national economies, global trends, and their effects on societies.'),
('ECON-201', 'Intermediate Microeconomics', 'Analyze advanced models of market behavior and their practical applications.'),
('ECON-202', 'Econometrics', 'Apply statistical methods to interpret and analyze economic data for policy decisions.'),
('ECON-301', 'Development Economics', 'Explore economic growth, poverty, and challenges in developing nations.'),
('ECON-302', 'Behavioral Economics', 'Study psychological factors influencing economic decisions and behaviors.'),
('ENGL-101', 'Introduction to Literature', 'Discover literary genres, techniques, and their cultural and historical significance.'),
('ENGL-102', 'Creative Writing Basics', 'Learn storytelling, poetic expression, and dramatic techniques for creative writing.'),
('ENGL-201', 'Shakespeare and His World', 'Explore Shakespeares works and their impact on literature and culture.'),
('ENGL-202', 'Modernist Literature', 'Analyze key texts and themes from the modernist literary movement.'),
('ENGL-301', 'American Literature', 'Survey major works and movements in American literature from colonial times to the present.'),
('ENGL-302', 'Postcolonial Studies', 'Examine literature from nations shaped by colonial history and their cultural narratives.'),
('PHIL-101', 'Introduction to Philosophy', 'Explore fundamental philosophical questions about existence, reality, and human understanding.'),
('PHIL-102', 'Critical Thinking', 'Develop skills in logical reasoning, argument analysis, and evidence evaluation.'),
('PHIL-201', 'Ethics', 'Study moral theories, ethical dilemmas, and their application in real-world situations.'),
('PHIL-202', 'Philosophy of Science', 'Analyze the principles and implications of scientific methods and discoveries.'),
('PHIL-301', 'Metaphysics', 'Examine the nature of reality, existence, and fundamental aspects of being.'),
('PHIL-302', 'Philosophy of Mind', 'Study consciousness, cognition, and their relationship to the physical world.'),
('POLI-101', 'Introduction to Politics', 'Understand political systems, ideologies, and their impacts on society and governance.'),
('POLI-102', 'Global Governance', 'Examine the functions and challenges of international organizations and global political frameworks.'),
('POLI-201', 'Public Policy', 'Analyze how policies are formulated, implemented, and evaluated in various political contexts.'),
('POLI-202', 'Political Theory', 'Explore key political thinkers, their ideas, and their influence on modern governance.'),
('POLI-301', 'Comparative Politics', 'Compare and contrast political systems, processes, and institutions across different nations.'),
('POLI-302', 'International Relations', 'Study the dynamics of global politics, diplomacy, and international cooperation.');


CREATE TABLE `prerequisiteof` (
  `course_code` varchar(50) NOT NULL,
  `prerequisite_course_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `prerequisiteof` (`course_code`, `prerequisite_course_code`) VALUES
('BIOL-201', 'BIOL-101'),
('BIOL-202', 'BIOL-102'),
('BIOL-301', 'BIOL-201'),
('BIOL-302', 'BIOL-202'),
('COMP-201', 'COMP-101'),
('COMP-202', 'COMP-102'),
('COMP-301', 'COMP-201'),
('COMP-302', 'COMP-202'),
('ECON-201', 'ECON-101'),
('ECON-202', 'ECON-102'),
('ECON-301', 'ECON-201'),
('ECON-302', 'ECON-202'),
('ENGL-201', 'ENGL-101'),
('ENGL-202', 'ENGL-102'),
('ENGL-301', 'ENGL-201'),
('ENGL-302', 'ENGL-202'),
('PHIL-201', 'PHIL-101'),
('PHIL-202', 'PHIL-102'),
('PHIL-301', 'PHIL-201'),
('PHIL-302', 'PHIL-202'),
('POLI-201', 'POLI-101'),
('POLI-202', 'POLI-102'),
('POLI-301', 'POLI-201'),
('POLI-302', 'POLI-202');


CREATE TABLE `degree_courses` (
  `id` int(11) NOT NULL,
  `degree_id` int(11) NOT NULL,
  `course_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `degree_courses` (`id`, `degree_id`, `course_code`) VALUES
(1, 1,  'COMP-101'),
(2, 1,  'COMP-102'),
(3, 1,  'COMP-201'),
(4, 1,  'COMP-202'),
(5, 1,  'COMP-301'),
(6, 1,  'COMP-302'),
(7, 2,  'COMP-101'),
(8, 2,  'COMP-102'),
(9, 2,  'COMP-201'),
(10, 2, 'COMP-202'),

(11, 3, 'BIOL-101'),
(12, 3, 'BIOL-102'),
(13, 3, 'BIOL-201'),
(14, 3, 'BIOL-202'),
(15, 3, 'BIOL-301'),
(16, 3, 'BIOL-302'),
(17, 4, 'BIOL-101'),
(18, 4, 'BIOL-102'),
(19, 4, 'BIOL-201'),
(20, 4, 'BIOL-202'),

(21, 5, 'POLI-101'),
(22, 5, 'POLI-102'),
(23, 5, 'POLI-201'),
(24, 5, 'POLI-202'),
(25, 5, 'POLI-301'),
(26, 5, 'POLI-302'),
(27, 6, 'POLI-101'),
(28, 6, 'POLI-102'),
(29, 6, 'POLI-201'),
(30, 6, 'POLI-202'),

(31, 7, 'ECON-101'),
(32, 7, 'ECON-102'),
(33, 7, 'ECON-201'),
(34, 7, 'ECON-202'),
(35, 7, 'ECON-301'),
(36, 7, 'ECON-302'),
(37, 8, 'ECON-101'),
(38, 8, 'ECON-102'),
(39, 8, 'ECON-201'),
(40, 8, 'ECON-202'),

(41, 9,  'PHIL-101'),
(42, 9,  'PHIL-102'),
(43, 9,  'PHIL-201'),
(44, 9,  'PHIL-202'),
(45, 9,  'PHIL-301'),
(46, 9,  'PHIL-302'),
(47, 10, 'PHIL-101'),
(48, 10, 'PHIL-102'),
(49, 10, 'PHIL-201'),
(50, 10, 'PHIL-202'),

(51, 11, 'ENGL-101'),
(52, 11, 'ENGL-102'),
(53, 11, 'ENGL-201'),
(54, 11, 'ENGL-202'),
(55, 11, 'ENGL-301'),
(56, 11, 'ENGL-302'),
(57, 12, 'ENGL-101'),
(58, 12, 'ENGL-102'),
(59, 12, 'ENGL-201'),
(60, 12, 'ENGL-202');


CREATE TABLE `sections` (
  `section_code` int(11) NOT NULL,
  `semester` varchar(11) NOT NULL,
  `professor` varchar(100) DEFAULT NULL,
  `course_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `sections` (`section_code`, `semester`, `professor`, `course_code`) VALUES
(1,  'FALL',   'Don Miller', 'COMP-101'),
(2,  'FALL',   'Don Miller', 'COMP-102'),
(3,  'FALL',   'Don Miller', 'COMP-201'),
(4,  'FALL',   'Don Miller', 'COMP-202'),
(5,  'WINTER', 'Don Miller', 'COMP-101'),
(6,  'WINTER', 'Don Miller', 'COMP-102'),
(7,  'WINTER', 'Don Miller', 'COMP-301'),
(8,  'WINTER', 'Don Miller', 'COMP-302'),
(9,  'SUMMER', 'Don Miller', 'COMP-101'),
(10, 'SUMMER', 'Don Miller', 'COMP-102'),

(11, 'FALL',   'John Green', 'BIOL-101'),
(12, 'FALL',   'John Green', 'BIOL-102'),
(13, 'FALL',   'John Green', 'BIOL-201'),
(14, 'FALL',   'John Green', 'BIOL-202'),
(15, 'WINTER', 'John Green', 'BIOL-101'),
(16, 'WINTER', 'John Green', 'BIOL-102'),
(17, 'WINTER', 'John Green', 'BIOL-301'),
(18, 'WINTER', 'John Green', 'BIOL-302'),
(19, 'SUMMER', 'John Green', 'BIOL-101'),
(20, 'SUMMER', 'John Green', 'BIOL-102'),

(21, 'FALL',   'Mike Doe',   'POLI-101'),
(22, 'FALL',   'Mike Doe',   'POLI-102'),
(23, 'FALL',   'Mike Doe',   'POLI-201'),
(24, 'FALL',   'Mike Doe',   'POLI-202'),
(25, 'WINTER', 'Mike Doe',   'POLI-101'),
(26, 'WINTER', 'Mike Doe',   'POLI-102'),
(27, 'WINTER', 'Mike Doe',   'POLI-301'),
(28, 'WINTER', 'Mike Doe',   'POLI-302'),
(29, 'SUMMER', 'Mike Doe',   'POLI-101'),
(30, 'SUMMER', 'Mike Doe',   'POLI-102'),

(31, 'FALL',   'Anna Lopez', 'ECON-101'),
(32, 'FALL',   'Anna Lopez', 'ECON-102'),
(33, 'FALL',   'Anna Lopez', 'ECON-201'),
(34, 'FALL',   'Anna Lopez', 'ECON-202'),
(35, 'WINTER', 'Anna Lopez', 'ECON-101'),
(36, 'WINTER', 'Anna Lopez', 'ECON-102'),
(37, 'WINTER', 'Anna Lopez', 'ECON-301'),
(38, 'WINTER', 'Anna Lopez', 'ECON-302'),
(39, 'SUMMER', 'Anna Lopez', 'ECON-101'),
(40, 'SUMMER', 'Anna Lopez', 'ECON-102'),

(41, 'FALL',   'Ivy Collin', 'PHIL-101'),
(42, 'FALL',   'Ivy Collin', 'PHIL-102'),
(43, 'FALL',   'Ivy Collin', 'PHIL-201'),
(44, 'FALL',   'Ivy Collin', 'PHIL-202'),
(45, 'WINTER', 'Ivy Collin', 'PHIL-101'),
(46, 'WINTER', 'Ivy Collin', 'PHIL-102'),
(47, 'WINTER', 'Ivy Collin', 'PHIL-301'),
(48, 'WINTER', 'Ivy Collin', 'PHIL-302'),
(49, 'SUMMER', 'Ivy Collin', 'PHIL-101'),
(50, 'SUMMER', 'Ivy Collin', 'PHIL-102'),

(51, 'FALL',   'Cara White', 'ENGL-101'),
(52, 'FALL',   'Cara White', 'ENGL-102'),
(53, 'FALL',   'Cara White', 'ENGL-201'),
(54, 'FALL',   'Cara White', 'ENGL-202'),
(55, 'WINTER', 'Cara White', 'ENGL-101'),
(56, 'WINTER', 'Cara White', 'ENGL-102'),
(57, 'WINTER', 'Cara White', 'ENGL-301'),
(58, 'WINTER', 'Cara White', 'ENGL-302'),
(59, 'SUMMER', 'Cara White', 'ENGL-101'),
(60, 'SUMMER', 'Cara White', 'ENGL-102');


CREATE TABLE `lectures` (
  `lecture_id` int(11) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `day_of_week` enum('Mon','Tue','Wed','Thu','Fri') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `section_code` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `lectures` (`lecture_id`, `location`, `day_of_week`, `start_time`, `end_time`, `section_code`) VALUES
(1,  'Thompson Hall 205', 'Mon', '9:00:00',  '10:00:00', 1),  /* COMP101 - Fall */
(2,  'Thompson Hall 205', 'Wed', '9:00:00',  '10:00:00', 1),  /* COMP101 - Fall */
(3,  'Ethics Center 101', 'Tue', '9:00:00',  '10:00:00', 2),  /* COMP102 - Fall */
(4,  'Ethics Center 101', 'Thu', '9:00:00',  '10:00:00', 2),  /* COMP102 - Fall */
(5,  'Darwin Laboratory 201', 'Tue', '12:00:00', '13:00:00', 3),  /* COMP201 - Fall */
(6,  'Darwin Laboratory 201', 'Thu', '12:00:00', '13:00:00', 3),  /* COMP201 - Fall */
(7,  'Civic Center Building 301', 'Wed', '12:00:00', '13:00:00', 4),  /* COMP202 - Fall */
(8,  'Civic Center Building 301', 'Fri', '12:00:00', '13:00:00', 4),  /* COMP202 - Fall */
(9,  'Maxwell Science Center 101', 'Mon', '9:00:00',  '10:00:00', 5),  /* COMP101 - Winter */
(10, 'Maxwell Science Center 101', 'Wed', '9:00:00',  '10:00:00', 5),  /* COMP101 - Winter */
(11, 'Keynes Hall 201', 'Tue', '9:00:00',  '10:00:00', 6),  /* COMP102 - Winter */
(12, 'Keynes Hall 201', 'Thu', '9:00:00',  '10:00:00', 6),  /* COMP102 - Winter */
(13, 'Literary Arts Center 301', 'Tue', '12:00:00', '13:00:00', 7),  /* COMP201 - Winter */
(14, 'Literary Arts Center 301', 'Thu', '12:00:00', '13:00:00', 7),  /* COMP201 - Winter */
(15, 'International Affairs Building 201', 'Wed', '12:00:00', '13:00:00', 8),  /* COMP202 - Winter */
(16, 'International Affairs Building 201', 'Fri', '12:00:00', '13:00:00', 8),  /* COMP202 - Winter */
(17, 'Summer Studies Complex 101', 'Mon', '12:00:00', '14:00:00', 9),  /* COMP101 - Summer */
(18, 'Summer Studies Complex 102', 'Tue', '12:00:00', '14:00:00', 10), /* COMP102 - Summer */

(21, 'Aristotle Building 201', 'Mon', '10:00:00', '11:00:00', 11), /* BIOL101 - Fall */
(22, 'Aristotle Building 201', 'Wed', '10:00:00', '11:00:00', 11), /* BIOL101 - Fall */
(23, 'Alan Turing Center 301', 'Tue', '10:00:00', '11:00:00', 12), /* BIOL102 - Fall */
(24, 'Alan Turing Center 301', 'Thu', '10:00:00', '11:00:00', 12), /* BIOL102 - Fall */
(25, 'Wordsworth Building 201', 'Tue', '13:00:00', '14:00:00', 13), /* BIOL201 - Fall */
(26, 'Wordsworth Building 201', 'Thu', '13:00:00', '14:00:00', 13), /* BIOL201 - Fall */
(27, 'Thompson Hall 301', 'Wed', '13:00:00', '14:00:00', 14), /* BIOL202 - Fall */
(28, 'Thompson Hall 301', 'Fri', '13:00:00', '14:00:00', 14), /* BIOL202 - Fall */
(29, 'Ethics Center 305', 'Mon', '10:00:00', '11:00:00', 15), /* BIOL101 - Winter */
(30, 'Ethics Center 305', 'Wed', '10:00:00', '11:00:00', 15), /* BIOL101 - Winter */
(31, 'Darwin Laboratory 305', 'Tue', '10:00:00', '11:00:00', 16), /* BIOL102 - Winter */
(32, 'Darwin Laboratory 305', 'Thu', '10:00:00', '11:00:00', 16), /* BIOL102 - Winter */
(33, 'Maxwell Science Center 205', 'Tue', '13:00:00', '14:00:00', 17), /* BIOL201 - Winter */
(34, 'Maxwell Science Center 205', 'Thu', '13:00:00', '14:00:00', 17), /* BIOL201 - Winter */
(35, 'Keynes Hall 305', 'Wed', '13:00:00', '14:00:00', 18), /* BIOL202 - Winter */
(36, 'Keynes Hall 305', 'Fri', '13:00:00', '14:00:00', 18), /* BIOL202 - Winter */
(37, 'Summer Studies Complex 101', 'Mon', '13:00:00', '15:00:00', 19), /* BIOL101 - Summer */
(38, 'Summer Studies Complex 102', 'Tue', '13:00:00', '15:00:00', 20), /* BIOL102 - Summer */

(41, 'Literary Arts Center 205', 'Mon', '11:00:00', '12:00:00', 21), /* POLI101 - Fall */
(42, 'Literary Arts Center 205', 'Wed', '11:00:00', '12:00:00', 21), /* POLI101 - Fall */
(43, 'Civic Center Building 205', 'Tue', '11:00:00', '12:00:00', 22), /* POLI102 - Fall */
(44, 'Civic Center Building 205', 'Thu', '11:00:00', '12:00:00', 22), /* POLI102 - Fall */
(45, 'International Affairs Building 305', 'Tue', '14:00:00', '15:00:00', 23), /* POLI201 - Fall */
(46, 'International Affairs Building 305', 'Thu', '14:00:00', '15:00:00', 23), /* POLI201 - Fall */
(47, 'Adam Smith Hall 301', 'Wed', '14:00:00', '15:00:00', 24), /* POLI202 - Fall */
(48, 'Adam Smith Hall 301', 'Fri', '14:00:00', '15:00:00', 24), /* POLI202 - Fall */
(49, 'Aristotle Building 305', 'Mon', '11:00:00', '12:00:00', 25), /* POLI101 - Winter */
(40, 'Aristotle Building 305', 'Wed', '11:00:00', '12:00:00', 25), /* POLI101 - Winter */
(51, 'Alan Turing Center 205', 'Tue', '11:00:00', '12:00:00', 26), /* POLI102 - Winter */
(52, 'Alan Turing Center 205', 'Thu', '11:00:00', '12:00:00', 26), /* POLI102 - Winter */
(53, 'Wordsworth Building 305', 'Tue', '14:00:00', '15:00:00', 27), /* POLI201 - Winter */
(54, 'Wordsworth Building 305', 'Thu', '14:00:00', '15:00:00', 27), /* POLI201 - Winter */
(55, 'Thompson Hall 412', 'Wed', '14:00:00', '15:00:00', 28), /* POLI202 - Winter */
(56, 'Thompson Hall 412', 'Fri', '14:00:00', '15:00:00', 28), /* POLI202 - Winter */
(57, 'Summer Studies Complex 101', 'Mon', '14:00:00', '16:00:00', 29), /* POLI101 - Summer */
(58, 'Summer Studies Complex 102', 'Tue', '14:00:00', '16:00:00', 30), /* POLI102 - Summer */

(61, 'Ethics Center 210', 'Mon', '13:00:00', '14:00:00', 31), /* ECON101 - Fall */
(62, 'Ethics Center 210', 'Wed', '13:00:00', '14:00:00', 31), /* ECON101 - Fall */
(63, 'Darwin Laboratory 102', 'Tue', '13:00:00', '14:00:00', 32), /* ECON102 - Fall */
(64, 'Darwin Laboratory 102', 'Thu', '13:00:00', '14:00:00', 32), /* ECON102 - Fall */
(65, 'Maxwell Science Center 301', 'Tue', '16:00:00', '17:00:00', 33), /* ECON201 - Fall */
(66, 'Maxwell Science Center 301', 'Thu', '16:00:00', '17:00:00', 33), /* ECON201 - Fall */
(67, 'Keynes Hall 401', 'Wed', '16:00:00', '17:00:00', 34), /* ECON202 - Fall */
(68, 'Keynes Hall 401', 'Fri', '16:00:00', '17:00:00', 34), /* ECON202 - Fall */
(69, 'Literary Arts Center 101', 'Mon', '13:00:00', '14:00:00', 35), /* ECON101 - Winter */
(70, 'Literary Arts Center 101', 'Wed', '13:00:00', '14:00:00', 35), /* ECON101 - Winter */
(71, 'Civic Center Building 401', 'Tue', '13:00:00', '14:00:00', 36), /* ECON102 - Winter */
(72, 'Civic Center Building 401', 'Thu', '13:00:00', '14:00:00', 36), /* ECON102 - Winter */
(73, 'International Affairs Building 401', 'Tue', '16:00:00', '17:00:00', 37), /* ECON201 - Winter */
(74, 'International Affairs Building 401', 'Thu', '16:00:00', '17:00:00', 37), /* ECON201 - Winter */
(75, 'Adam Smith Hall 205', 'Wed', '16:00:00', '17:00:00', 38), /* ECON202 - Winter */
(76, 'Adam Smith Hall 205', 'Fri', '16:00:00', '17:00:00', 38), /* ECON202 - Winter */
(77, 'Summer Studies Complex 101', 'Mon', '16:00:00', '18:00:00', 39), /* ECON101 - Summer */
(78, 'Summer Studies Complex 102', 'Tue', '16:00:00', '18:00:00', 40), /* ECON102 - Summer */

(81, 'Aristotle Building 401', 'Mon', '14:00:00', '15:00:00', 41), /* PHIL101 - Fall */
(82, 'Aristotle Building 401', 'Wed', '14:00:00', '15:00:00', 41), /* PHIL101 - Fall */
(83, 'Alan Turing Center 401', 'Tue', '14:00:00', '15:00:00', 42), /* PHIL102 - Fall */
(84, 'Alan Turing Center 401', 'Thu', '14:00:00', '15:00:00', 42), /* PHIL102 - Fall */
(85, 'Wordsworth Building 401', 'Tue', '17:00:00', '18:00:00', 43), /* PHIL201 - Fall */
(86, 'Wordsworth Building 401', 'Thu', '17:00:00', '18:00:00', 43), /* PHIL201 - Fall */
(87, 'Thompson Hall 401', 'Wed', '17:00:00', '18:00:00', 44), /* PHIL202 - Fall */
(88, 'Thompson Hall 401', 'Fri', '17:00:00', '18:00:00', 44), /* PHIL202 - Fall */
(89, 'Ethics Center 401', 'Mon', '14:00:00', '15:00:00', 45), /* PHIL101 - Winter */
(90, 'Ethics Center 401', 'Wed', '14:00:00', '15:00:00', 45), /* PHIL101 - Winter */
(91, 'Darwin Laboratory 401', 'Tue', '14:00:00', '15:00:00', 46), /* PHIL102 - Winter */
(92, 'Darwin Laboratory 401', 'Thu', '14:00:00', '15:00:00', 46), /* PHIL102 - Winter */
(93, 'Maxwell Science Center 401', 'Tue', '17:00:00', '18:00:00', 47), /* PHIL201 - Winter */
(94, 'Maxwell Science Center 401', 'Thu', '17:00:00', '18:00:00', 47), /* PHIL201 - Winter */
(95, 'Keynes Hall 105', 'Wed', '17:00:00', '18:00:00', 48), /* PHIL202 - Winter */
(96, 'Keynes Hall 105', 'Fri', '17:00:00', '18:00:00', 48), /* PHIL202 - Winter */
(97, 'Summer Studies Complex 101', 'Mon', '17:00:00', '19:00:00', 49), /* PHIL101 - Summer */
(98, 'Summer Studies Complex 102', 'Tue', '17:00:00', '19:00:00', 50), /* PHIL102 - Summer */

(101, 'Adam Smith Hall 101', 'Mon', '14:00:00', '15:00:00', 51), /* ENGL101 - Fall */
(102, 'Adam Smith Hall 101', 'Wed', '14:00:00', '15:00:00', 51), /* ENGL101 - Fall */
(103, 'Thompson Hall 218', 'Tue', '14:00:00', '15:00:00', 52), /* ENGL102 - Fall */
(104, 'Thompson Hall 218', 'Thu', '14:00:00', '15:00:00', 52), /* ENGL102 - Fall */
(105, 'Ethics Center 315', 'Tue', '17:00:00', '18:00:00', 53), /* ENGL201 - Fall */
(106, 'Ethics Center 315', 'Thu', '17:00:00', '18:00:00', 53), /* ENGL201 - Fall */
(107, 'Darwin Laboratory 205', 'Wed', '17:00:00', '18:00:00', 54), /* ENGL202 - Fall */
(108, 'Darwin Laboratory 205', 'Fri', '17:00:00', '18:00:00', 54), /* ENGL202 - Fall */
(109, 'Maxwell Science Center 105', 'Mon', '14:00:00', '15:00:00', 55), /* ENGL101 - Winter */
(110, 'Maxwell Science Center 105', 'Wed', '14:00:00', '15:00:00', 55), /* ENGL101 - Winter */
(111, 'International Affairs Building 105', 'Tue', '14:00:00', '15:00:00', 56), /* ENGL102 - Winter */
(112, 'International Affairs Building 105', 'Thu', '14:00:00', '15:00:00', 56), /* ENGL102 - Winter */
(113, 'Aristotle Building 105', 'Tue', '17:00:00', '18:00:00', 57), /* ENGL201 - Winter */
(114, 'Aristotle Building 105', 'Thu', '17:00:00', '18:00:00', 57), /* ENGL201 - Winter */
(115, 'Literary Arts Center 401', 'Wed', '17:00:00', '18:00:00', 58), /* ENGL202 - Winter */
(116, 'Literary Arts Center 401', 'Fri', '17:00:00', '18:00:00', 58), /* ENGL202 - Winter */
(117, 'Summer Studies Complex 101', 'Mon', '17:00:00', '19:00:00', 59), /* ENGL101 - Summer */
(118, 'Summer Studies Complex 102', 'Tue', '17:00:00', '19:00:00', 60); /* ENGL102 - Summer */


CREATE TABLE `coursescompleted` (
  `student_id` int(11) NOT NULL,
  `course_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `coursescompleted` (`student_id`, `course_code`) VALUES
(1,  'COMP-101'),
(1,  'COMP-102'),
(2,  'COMP-101'),
(2,  'COMP-102'),
(3,  'COMP-101'),
(3,  'COMP-102'),
(4,  'COMP-101'),
(4,  'COMP-102'),
(5,  'COMP-101'),
(5,  'COMP-102'),
(6,  'COMP-101'),
(6,  'COMP-102'),
(7,  'COMP-101'),
(7,  'COMP-102'),
(8,  'COMP-101'),
(8,  'COMP-102'),
(9,  'COMP-101'),
(9,  'COMP-102'),
(10, 'BIOL-101'),
(10, 'BIOL-102'),
(10, 'BIOL-201'),
(10, 'COMP-101'),
(10, 'COMP-102'),
(10, 'COMP-201'),
(10, 'COMP-202');


CREATE TABLE `coursesenrolled` (
  `student_id` int(11) NOT NULL,
  `section_code` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `coursesenrolled` (`student_id`, `section_code`) VALUES
(1, 3),
(3, 3),
(4, 3),
(5, 3),
(6, 3),
(7, 3),
(8, 3),
(9, 3);


CREATE TABLE `friendrequests` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `friendswith` (
  `id` int(11) NOT NULL,
  `student_id1` int(11) NOT NULL,
  `student_id2` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_code`);

ALTER TABLE `coursescompleted`
  ADD PRIMARY KEY (`student_id`,`course_code`),
  ADD KEY `fk_completed_course` (`course_code`);

ALTER TABLE `coursesenrolled`
  ADD PRIMARY KEY (`student_id`,`section_code`),
  ADD KEY `fk_enrolled_section` (`section_code`);

ALTER TABLE `degrees`
  ADD PRIMARY KEY (`degree_id`),
  ADD UNIQUE KEY `unique_degree` (`name`,`type`);

ALTER TABLE `degree_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_degree_idx` (`degree_id`),
  ADD KEY `fk_course_idx` (`course_code`);

ALTER TABLE `friendrequests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_request` (`sender_id`,`receiver_id`),
  ADD KEY `receiver_id` (`receiver_id`);

ALTER TABLE `friendswith`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_friendship` (`student_id1`,`student_id2`),
  ADD KEY `student_id2` (`student_id2`);

ALTER TABLE `lectures`
  ADD PRIMARY KEY (`lecture_id`),
  ADD KEY `fk_section_code` (`section_code`);

ALTER TABLE `prerequisiteof`
  ADD PRIMARY KEY (`course_code`,`prerequisite_course_code`),
  ADD KEY `fk_prerequisite` (`prerequisite_course_code`);

ALTER TABLE `sections`
  ADD PRIMARY KEY (`section_code`),
  ADD KEY `fk_course_code` (`course_code`);

ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_major` (`major_id`),
  ADD KEY `fk_minor` (`minor_id`);

ALTER TABLE `degrees`
  MODIFY `degree_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `degree_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

ALTER TABLE `friendrequests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `friendswith`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `lectures`
  MODIFY `lecture_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9873;

ALTER TABLE `sections`
  MODIFY `section_code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8302;

ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

ALTER TABLE `coursescompleted`
  ADD CONSTRAINT `fk_completed_course` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_completed_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

ALTER TABLE `coursesenrolled`
  ADD CONSTRAINT `fk_enrolled_section` FOREIGN KEY (`section_code`) REFERENCES `sections` (`section_code`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enrolled_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

ALTER TABLE `degree_courses`
  ADD CONSTRAINT `fk_course` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_degree` FOREIGN KEY (`degree_id`) REFERENCES `degrees` (`degree_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `friendrequests`
  ADD CONSTRAINT `friendrequests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friendrequests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

ALTER TABLE `friendswith`
  ADD CONSTRAINT `friendswith_ibfk_1` FOREIGN KEY (`student_id1`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friendswith_ibfk_2` FOREIGN KEY (`student_id2`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

ALTER TABLE `lectures`
  ADD CONSTRAINT `fk_section_code` FOREIGN KEY (`section_code`) REFERENCES `sections` (`section_code`) ON DELETE CASCADE;

ALTER TABLE `prerequisiteof`
  ADD CONSTRAINT `fk_prerequisite` FOREIGN KEY (`prerequisite_course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_prerequisite_course` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE;

ALTER TABLE `sections`
  ADD CONSTRAINT `fk_course_code` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`) ON DELETE CASCADE;

ALTER TABLE `students`
  ADD CONSTRAINT `fk_major` FOREIGN KEY (`major_id`) REFERENCES `degrees` (`degree_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_minor` FOREIGN KEY (`minor_id`) REFERENCES `degrees` (`degree_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
