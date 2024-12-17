<!-- coded by Tim Galyanov -->
<?php
include '../includes/config.php';
include '../includes/functions.php';

header('Content-Type: application/json');

$query = $_GET['query'] ?? '';
if (strlen($query) < 2) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT course_code, course_name 

        FROM courses
        WHERE course_code LIKE ? OR course_name LIKE ?
        LIMIT 5";
$stmt = $conn->prepare($sql);
$like = "%$query%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode($courses);
