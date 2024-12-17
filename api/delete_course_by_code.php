<!-- coded by Tim Galyanov -->
<?php

include '../includes/config.php';
include '../includes/functions.php';

session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Not logged in"]);
    exit();
}
$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$courseCode = trim($input['course_code'] ?? '');
if (empty($courseCode)) {
    echo json_encode(["error" => "Course code is required."]);
    exit();
}
$conn->begin_transaction();

try {
    $sqlSections = "
    SELECT ce.section_code
    FROM coursesenrolled ce
    JOIN sections s ON ce.section_code = s.section_code
    WHERE ce.student_id = ?
      AND s.course_code = ?";
    $stmtSec = $conn->prepare($sqlSections);    
    $stmtSec->bind_param("is", $user_id, $courseCode);
    $stmtSec->execute();
    $resultSections = $stmtSec->get_result();
    
    $secCode = [];
    while ($row = $resultSections->fetch_assoc()) {
        $secCode[] = $row['section_code'];
    }
    
    $stmtSec->close();
    
    if (empty($secCode)) {
        throw new Exception("No enrolled sections found for the specified course.");
    }
    $ph = implode(',', array_fill(0, count($secCode), '?'));
    $types = str_repeat('i', count($secCode));
    $sqlDelete = "DELETE FROM coursesenrolled WHERE student_id = ? AND section_code IN ($ph)";
    $stmtDelete = $conn->prepare($sqlDelete);
    
    $stmtDelete->bind_param('i' . $types, $user_id, ...$secCode);
    $stmtDelete->execute();
    if ($stmtDelete->affected_rows === 0) {
        throw new Exception("Failed to delete enrolled courses.");
    }
    
    $stmtDelete->close();
    $conn->commit();
    
    echo json_encode(["success" => "Course deleted successfully."]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error deleting course: " . $e->getMessage());
    echo json_encode(["error" => $e->getMessage()]);
}
?>
