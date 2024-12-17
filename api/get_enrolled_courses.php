<!-- coded by Tim Galyanov -->
<?php 
include '../includes/config.php';
include '../includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
if (isset($_GET['semester']) && $_GET['semester'] !== null) {
    $semesterName = trim($_GET['semester']);
} else {
    $semesterName = 'Fall';
}

if (empty($semesterName)) {
    echo json_encode(["error" => "Semester parameter is required."]);
    exit();
}

$sql = "
SELECT DISTINCT 
    c.course_code AS code,
    c.course_name AS name,
    s.professor AS professor
FROM coursesenrolled ce
JOIN sections s ON ce.section_code = s.section_code
JOIN courses c ON s.course_code = c.course_code
WHERE ce.student_id = ?
  AND LOWER(s.semester) = LOWER(?)
";

$stmt = $conn->prepare($sql);


$stmt->bind_param("is", $user_id, $semesterName);
$stmt->execute();
$result = $stmt->get_result();

$enrolledCourses = [];
while ($row = $result->fetch_assoc()) {
    $enrolledCourses[] = $row;
}

$stmt->close();

echo json_encode($enrolledCourses);
?>
