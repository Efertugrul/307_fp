<!-- coded by Tim Galyanov -->
<?php

include '../includes/config.php';
include '../includes/functions.php';

header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized. Please log in.']);
    exit();
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

$input = json_decode(file_get_contents('php://input'), true);
$section_code = isset($input['section_code']) ? intval($input['section_code']) : 0;
$semesterName = trim($input['semester'] ?? 'Fall');


if ($section_code <= 0 || empty($semesterName)) {
    echo json_encode(["success" => false, "error" => "Invalid parameters."]);
    exit();
}

$sqlCheck = "SELECT course_code FROM sections WHERE section_code = ? AND LOWER(semester) = LOWER(?)";
$stmtCheck = $conn->prepare($sqlCheck);

$stmtCheck->bind_param("is", $section_code, $semesterName);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows === 0) {
    $stmtCheck->close();
    echo json_encode(["success" => false, "error" => "Section not found or not available for the selected semester."]);
    exit();
}

$sectionData = $resultCheck->fetch_assoc();
$course_code = strtoupper(trim($sectionData['course_code'])); 

$stmtCheck->close();

$sqlDuplicateEnrollment = "
    SELECT ce.section_code, s.semester 
    FROM coursesenrolled ce
    JOIN sections s ON ce.section_code = s.section_code
    WHERE ce.student_id = ? AND s.course_code = ? AND LOWER(s.semester) != LOWER(?)
";
$stmtDup = $conn->prepare($sqlDuplicateEnrollment);





$stmtDup->bind_param("iss", $user_id, $course_code, $semesterName);
$stmtDup->execute();
$resultDup = $stmtDup->get_result();

if ($resultDup->num_rows > 0) {
    $existingEnrollments = [];
    while ($row = $resultDup->fetch_assoc()) {
        $existingEnrollments[] = [
            'section_code' => $row['section_code'],
            'semester' => ucfirst(strtolower($row['semester'])) 
        ];
    }
    $stmtDup->close();
    $errorCourses = array_map(function($enrollment) use ($course_code) {
        return " {$enrollment['semester']}";
    }, $existingEnrollments);

    $errorMessage = "You are already enrolled in {$course_code} in the"  . implode(', ', $errorCourses) . " semester. Please remove the existing enrollment before adding it to another semester.";

    echo json_encode([
        "success" => false,
        "error" => $errorMessage
    ]);
    exit();
}

$stmtDup->close();
$sqlEnrolled = "SELECT * FROM coursesenrolled WHERE student_id = ? AND section_code = ?";
$stmtEnrolled = $conn->prepare($sqlEnrolled);


$stmtEnrolled->bind_param("ii", $user_id, $section_code);
$stmtEnrolled->execute();
$resultEnrolled = $stmtEnrolled->get_result();

if ($resultEnrolled->num_rows > 0) {
    $stmtEnrolled->close();
    echo json_encode(["success" => false, "error" => "You have already enrolled in this course section."]);
    exit();
}

$stmtEnrolled->close();
function normalizeCourseCode($code) {
    return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', trim($code)));
}

$sqlPrereq = "SELECT UPPER(TRIM(prerequisite_course_code)) AS prerequisite_course_code FROM prerequisiteof WHERE course_code = ?";
$stmtPrereq = $conn->prepare($sqlPrereq);
if (!$stmtPrereq) {
    error_log("Query preparation failed: " . $conn->error);
    echo json_encode(["success" => false, "error" => "Database error while fetching prerequisites."]);
    exit();
}

$stmtPrereq->bind_param("s", $course_code);
$stmtPrereq->execute();
$prerequisites = $stmtPrereq->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtPrereq->close();

$prerequisites = array_column($prerequisites, 'prerequisite_course_code');
error_log("Prerequisites for course {$course_code}: " . implode(', ', $prerequisites));

$missingPrereqs = [];
if (!empty($prerequisites)) {
    $sqlCompleted = "SELECT UPPER(TRIM(course_code)) AS course_code FROM coursescompleted WHERE student_id = ? AND course_code IN ('" . implode("','", $prerequisites) . "')";
    $stmtCompleted = $conn->prepare($sqlCompleted);
    $stmtCompleted->bind_param("i", $user_id);
    $stmtCompleted->execute();
    $completedCourses = $stmtCompleted->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtCompleted->close();

    $completedCourses = array_column($completedCourses, 'course_code');
    error_log("Completed courses for user {$user_id}: " . implode(', ', $completedCourses));
    $missingPrereqs = array_diff($prerequisites, $completedCourses);
    if (!empty($missingPrereqs)) {
        error_log("Missing prerequisites for course {$course_code}: " . implode(', ', $missingPrereqs));
    } else {
        error_log("All prerequisites completed for course {$course_code}.");
    }
}
function timesOverlap($day1, $start1, $end1, $day2, $start2, $end2) {
    if (strcasecmp($day1, $day2) !== 0) { 
        return false;
    }

    return ($start1 < $end2) && ($start2 < $end1);
}

$sqlNews = "SELECT day_of_week, start_time, end_time FROM lectures WHERE section_code = ?";
$stmtNews = $conn->prepare($sqlNews);
$stmtNews->bind_param("i", $section_code);
$stmtNews->execute();
$resultNews = $stmtNews->get_result();

$newLectures = [];
while ($row = $resultNews->fetch_assoc()) {
    $newLectures[] = [
        'day_of_week' => strtoupper(trim($row['day_of_week'])), 'start_time' => $row['start_time'],
        'end_time' => $row['end_time']
    ];
}
$stmtNews->close();

$sqlExistingEnrollments = "
    SELECT ce.section_code, c.course_code, c.course_name
    FROM coursesenrolled ce
    JOIN sections s ON ce.section_code = s.section_code
    JOIN courses c ON s.course_code = c.course_code
    WHERE ce.student_id = ? AND LOWER(s.semester) = LOWER(?)
";
$stmtExistingEnrollments = $conn->prepare($sqlExistingEnrollments);

$stmtExistingEnrollments->bind_param("is", $user_id, $semesterName);
$stmtExistingEnrollments->execute();
$resultExistingEnrollments = $stmtExistingEnrollments->get_result();

$existingEnrollments = [];
while ($row = $resultExistingEnrollments->fetch_assoc()) {
    $existingEnrollments[] = [
        'section_code' => $row['section_code'],'course_code' => strtoupper(trim($row['course_code'])), 
        'course_name' => $row['course_name']
    ];
}

$stmtExistingEnrollments->close();

$conflictingCourses = [];

foreach ($existingEnrollments as $enrollment) {
    $sqlExisting = "SELECT day_of_week, start_time, end_time FROM lectures WHERE section_code = ?";
    $stmtExisting = $conn->prepare($sqlExisting);

    $stmtExisting->bind_param("i", $enrollment['section_code']);
    $stmtExisting->execute();
    $resultExistingLectures = $stmtExisting->get_result();

    while ($row = $resultExistingLectures->fetch_assoc()) {
        $existingLecture = [
            'day_of_week' => strtoupper(trim($row['day_of_week'])), 'start_time' => $row['start_time'],
            'end_time' => $row['end_time']
        ];
        foreach ($newLectures as $newLecture) {
            if (timesOverlap(
                $newLecture['day_of_week'], $newLecture['start_time'],$newLecture['end_time'],
                $existingLecture['day_of_week'],$existingLecture['start_time'],$existingLecture['end_time']
            )) {
                if (!in_array($enrollment['course_code'], array_column($conflictingCourses, 'course_code'))) {
                    $conflictingCourses[] = [
                        'course_code' => $enrollment['course_code'],
                        'course_name' => $enrollment['course_name']
                    ];
                }
            }
        }
    }

    $stmtExisting->close();
}
if (!empty($conflictingCourses)) {
    echo json_encode([
        "success" => false,
        "error" => "Schedule conflict detected with course(s): " . implode(', ', array_map(function($c) {
            return "{$c['course_code']} ({$c['course_name']})";
        }, $conflictingCourses)) . ".",
    ]);
    exit();
}

$sql_insert = "
    INSERT INTO coursesenrolled (student_id, section_code)
    VALUES (?, ?)
";
$stmt_insert = $conn->prepare($sql_insert);

$stmt_insert->bind_param("ii", $user_id, $section_code);

if ($stmt_insert->execute()) {
    $response = [
        "success" => true,
        "message" => "Course added successfully.",
        "inserted" => 1
    ];

    if (!empty($missingPrereqs)) {
        $placeholders = implode(',', array_fill(0, count($missingPrereqs), '?'));
        $types_prereq = str_repeat('s', count($missingPrereqs));
        $sqlCourseNames = "SELECT course_name FROM courses WHERE course_code IN ($placeholders)";
        $stmtCourseNames = $conn->prepare($sqlCourseNames);
        if ($stmtCourseNames) {
            $types_bind_prereq = $types_prereq;
            $bind_course_params = [];
            $bind_course_params[] = $types_prereq;
            foreach ($missingPrereqs as $prereq) {
                $bind_course_params[] = &$prereq; 
            }

            call_user_func_array([$stmtCourseNames, 'bind_param'], $bind_course_params);
            $stmtCourseNames->execute();
            $resultCourseNames = $stmtCourseNames->get_result();
            $missingCourseNames = [];
            while ($row = $resultCourseNames->fetch_assoc()) {
                $missingCourseNames[] = $row['course_name'];
            }
            $stmtCourseNames->close();

            $response["warning"] = "You have not completed the following prerequisite(s): " . implode(', ', $missingCourseNames) . ".";
        } else {
            $response["warning"] = "You have not completed the following prerequisite(s): " . implode(', ', $missingPrereqs) . ".";
        }
    }

    echo json_encode($response);
} else {
    echo json_encode(["success" => false, "error" => "Failed to add course to schedule."]);
}

$stmt_insert->close();
$conn->close();
?>
