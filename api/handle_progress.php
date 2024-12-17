<!-- coded by Jaiten Kang -->
<?php

session_start();
header('Content-Type: text/html; charset=utf-8');

include '../includes/config.php';
include '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to manage your progress.";
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['course_code'])) {
        $course_code = strtoupper(trim($_POST['course_code']));

        if (empty($course_code)) {
            $_SESSION['add_course_error'] = "Course code cannot be empty.";
            header("Location: ../public/my_progress.php");
            exit();
        }

        if (!preg_match('/^[A-Z]{2,4}-\d{3}$/', $course_code)) {
            $_SESSION['add_course_error'] = "Invalid course code format. Please enter in the format XXXX-XXX.";
            header("Location: ../public/my_progress.php");
            exit();
        }

        $conn->begin_transaction();
        $conn->begin_transaction();
        $conn->begin_transaction();
        $conn->begin_transaction();

        try {
          
          
            $sqlCheckCourse = "SELECT course_code FROM courses WHERE course_code = ?";
            $stmtCheckCourse = $conn->prepare($sqlCheckCourse);
          
            $stmtCheckCourse->bind_param("s", $course_code);
            $stmtCheckCourse->execute();
            $resultCheckCourse = $stmtCheckCourse->get_result();
            if ($resultCheckCourse->num_rows === 0) {
                $stmtCheckCourse->close();
                throw new Exception("The course code '{$course_code}' does not exist.");
            }
            $stmtCheckCourse->close();

            $sqlCheckCompleted = "SELECT 1 FROM coursescompleted WHERE student_id = ? AND course_code = ?";
            $stmtCheckCompleted = $conn->prepare($sqlCheckCompleted);
            if (!$stmtCheckCompleted) {
                throw new Exception("Database error while checking completion status: " . $conn->error);
            }

            $stmtCheckCompleted->bind_param("is", $user_id, $course_code);
            $stmtCheckCompleted->execute();
            $resultCompleted = $stmtCheckCompleted->get_result();

            if ($resultCompleted->num_rows > 0) {
                $stmtCheckCompleted->close();
                throw new Exception("You have already marked '{$course_code}' as completed.");
            }
            $stmtCheckCompleted->close();

            $sqlInsertCompleted = "INSERT INTO coursescompleted (student_id, course_code) VALUES (?, ?)";
            $stmtInsertCompleted = $conn->prepare($sqlInsertCompleted);
            if (!$stmtInsertCompleted) {
                throw new Exception("Database error while inserting completed course: " . $conn->error);
            }

            $stmtInsertCompleted->bind_param("is", $user_id, $course_code);

            if (!$stmtInsertCompleted->execute()) {
                throw new Exception("Failed to mark course as completed. Please try again.");
            }
            $stmtInsertCompleted->close();


            $sqlCheckEnrollment = "
                SELECT ce.section_code, s.semester, c.course_name
                FROM coursesenrolled ce
                JOIN sections s ON ce.section_code = s.section_code
                JOIN courses c ON s.course_code = c.course_code
                WHERE ce.student_id = ? AND c.course_code = ?
            ";
            $stmtCheckEnrollment = $conn->prepare($sqlCheckEnrollment);
            if (!$stmtCheckEnrollment) {
                throw new Exception("Database error while checking existing enrollments: " . $conn->error);
            }
            $stmtCheckEnrollment->bind_param("is", $user_id, $course_code);
            $stmtCheckEnrollment->execute();
            $resultEnrollment = $stmtCheckEnrollment->get_result();

            $existingEnrollments = [];
            while ($row = $resultEnrollment->fetch_assoc()) {
                $existingEnrollments[] = [
                    'section_code' => $row['section_code'],
                    'semester' => ucfirst(strtolower($row['semester'])),
                    'course_name' => $row['course_name']
                ];
            }
            $stmtCheckEnrollment->close();

            if (!empty($existingEnrollments)) {
                $section_codes = array_column($existingEnrollments, 'section_code');
                $ph = implode(',', array_fill(0, count($section_codes), '?'));
                $types = str_repeat('i', count($section_codes));
                $sqlDeleteEnrollments = "DELETE FROM coursesenrolled WHERE student_id = ? AND section_code IN ($ph)";
                $stmtDeleteEnrollments = $conn->prepare($sqlDeleteEnrollments);

                $params = array_merge([$user_id], $section_codes);
                $types_bind = 'i' . $types;

                $refs = [];
                foreach ($params as $key => $value) {
                    $refs[$key] = &$params[$key];
                }

                array_unshift($refs, $types_bind);
                call_user_func_array([$stmtDeleteEnrollments, 'bind_param'], $refs);

                if (!$stmtDeleteEnrollments->execute()) {
                    throw new Exception("Database error while deleting enrollments: " . $stmtDeleteEnrollments->error);
                }
                $stmtDeleteEnrollments->close();

                foreach ($existingEnrollments as $enrollment) {
                    error_log("Auto-removed enrollment: User ID {$user_id} removed from course '{$course_code}' ({$enrollment['course_name']}) in {$enrollment['semester']} semester (Section Code: {$enrollment['section_code']}).");
                }

                $removedCourses = array_map(function($enrollment) use ($course_code) {
                    return "{$course_code} ({$enrollment['course_name']}) in {$enrollment['semester']} semester (Section Code: {$enrollment['section_code']})";
                }, $existingEnrollments);

                $_SESSION['add_course_success'] = "Course '{$course_code}' marked as completed successfully. Existing enrollments for this course have been removed: " . implode(', ', $removedCourses) . ".";
            } else {
                $_SESSION['add_course_success'] = "Course '{$course_code}' marked as completed successfully.";
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();

            error_log("Error in handle_progress.php (add): " . $e->getMessage());

            $_SESSION['add_course_error'] = $e->getMessage();
        }

        header("Location: ../public/my_progress.php");
        exit();
    } elseif (isset($_POST['remove_course_code'])) {
        $course_code = strtoupper(trim($_POST['remove_course_code']));

        if (empty($course_code)) {
            $_SESSION['remove_course_error'] = "Course code cannot be empty.";
            header("Location: ../public/my_progress.php");
            exit();
        }

        if (!preg_match('/^[A-Z]{2,4}-\d{3}$/', $course_code)) {
            $_SESSION['remove_course_error'] = "Invalid course code format. Please enter in the format XXXX-XXX.";
            header("Location: ../public/my_progress.php");
            exit();
        }

        try {
            $stmt_check = $conn->prepare("SELECT course_code FROM coursescompleted WHERE student_id = ? AND course_code = ?");
            if (!$stmt_check) {
                throw new Exception("Database error: " . $conn->error);
            }
            $stmt_check->bind_param("is", $user_id, $course_code);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows === 0) {
                $stmt_check->close();
                throw new Exception("The course '{$course_code}' is not marked as completed.");
            }
            $stmt_check->close();

            $stmt_delete = $conn->prepare("DELETE FROM coursescompleted WHERE student_id = ? AND course_code = ?");
           
            $stmt_delete->bind_param("is", $user_id, $course_code);
            if ($stmt_delete->execute()) {
                $_SESSION['remove_course_success'] = "Course '{$course_code}' has been successfully removed from your completed courses.";
            } else {
                throw new Exception("Failed to remove the course. Please try again.");
            }
            $stmt_delete->close();
        } catch (Exception $e) {
            error_log("Error in handle_progress.php (remove): " . $e->getMessage());

            $_SESSION['remove_course_error'] = $e->getMessage();
        }

        header("Location: ../public/my_progress.php");
        exit();
    } else {
        header("Location: ../public/my_progress.php");
        exit();
    }
}
?>
