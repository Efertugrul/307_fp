<!-- coded by Jaiten Kang -->
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to view your progress.";
    header("Location: my_progress.php"); 
    exit();
}

$user_id = $_SESSION['user_id'];

$sql_degrees = "SELECT major_id, minor_id FROM students WHERE student_id = ?";
$stmt_degrees = $conn->prepare($sql_degrees);
if (!$stmt_degrees) {
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    $_SESSION['error_message'] = "An error occurred. Please try again later.";
    header('Location: my_progress.php'); 
    exit();
}
$stmt_degrees->bind_param("i", $user_id);
$stmt_degrees->execute();
$stmt_degrees->bind_result($major_id, $minor_id);
$stmt_degrees->fetch();
$stmt_degrees->close();
$degrees = [];
$degree_labels = [];

if (!is_null($major_id)) {
    $degrees[] = $major_id;
    $degree_labels[] = "Major";
}

if (!is_null($minor_id)) {
    $degrees[] = $minor_id;
    $degree_labels[] = "Minor";
}

if (empty($degrees)) {
    $no_degrees_registered = true;
    return; 
} else {
    $no_degrees_registered = false;
}

$placeholders = implode(',', array_fill(0, count($degrees), '?'));
$sql_degree_names = "SELECT degree_id, name, type FROM degrees WHERE degree_id IN ($placeholders)";
$stmt_degree_names = $conn->prepare($sql_degree_names);


$types = str_repeat('i', count($degrees));
$stmt_degree_names->bind_param($types, ...$degrees);
$stmt_degree_names->execute();
$res_degree_names = $stmt_degree_names->get_result();
$degree_info = [];
while ($row = $res_degree_names->fetch_assoc()) {
    $degree_info[$row['degree_id']] = [
        'name' => $row['name'],
        'type' => $row['type']
    ];
}
$stmt_degree_names->close();

$sql_degree_courses = "SELECT degree_id, course_code FROM degree_courses WHERE degree_id IN ($placeholders)";
$stmt_degree_courses = $conn->prepare($sql_degree_courses);
if (!$stmt_degree_courses) {
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    $_SESSION['error_message'] = "An error occurred. Please try again later.";
    header('Location: my_progress.php'); 
    exit();
}
$stmt_degree_courses->bind_param($types, ...$degrees);
$stmt_degree_courses->execute();
$res_degree_courses = $stmt_degree_courses->get_result();
$required_courses = array();
$courses_to_fetch = array();

while ($row = $res_degree_courses->fetch_assoc()) {
    $upper_course_code = strtoupper($row['course_code']);
    $required_courses[$row['degree_id']][] = $upper_course_code;
    $courses_to_fetch[] = $upper_course_code;
}
$stmt_degree_courses->close();

$sql_completed = "
    SELECT c.course_code, c.course_name, c.course_description 
    FROM coursescompleted cc
    JOIN courses c ON cc.course_code = c.course_code
    WHERE cc.student_id = ?
    ORDER BY c.course_code
";
$stmt_completed = $conn->prepare($sql_completed);
if (!$stmt_completed) {
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    $_SESSION['error_message'] = "An error occurred. Please try again later.";
    header('Location: my_progress.php'); 
    exit();
}
$stmt_completed->bind_param("i", $user_id);
$stmt_completed->execute();
$res_completed = $stmt_completed->get_result();
$completedCourses = [];
$all_completed_course_details = [];
while ($row = $res_completed->fetch_assoc()) {
    $upper_code = strtoupper($row['course_code']);
    $completedCourses[] = $upper_code;
    $all_completed_course_details[$upper_code] = [
        'course_code' => $upper_code,
        'course_name' => $row['course_name'],
        'course_description' => $row['course_description']
    ];
}
$stmt_completed->close();

$completedCoursesJSON = json_encode($completedCourses);

$course_details = [];
if (!empty($courses_to_fetch)) {
    $placeholders_courses = implode(',', array_fill(0, count($courses_to_fetch), '?'));
    $sql_courses = "SELECT course_code, course_name, course_description FROM courses WHERE course_code IN ($placeholders_courses)";
    $stmt_courses = $conn->prepare($sql_courses);
    if (!$stmt_courses) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
        header('Location: my_progress.php'); 
        exit();
    }
    $types_courses = str_repeat('s', count($courses_to_fetch));
    $stmt_courses->bind_param($types_courses, ...$courses_to_fetch);
    $stmt_courses->execute();
    $res_courses = $stmt_courses->get_result();
    while ($row = $res_courses->fetch_assoc()) {
        $upper_code = strtoupper($row['course_code']);
        $course_details[$upper_code] = [
            'course_code' => $upper_code,
            'course_name' => $row['course_name'],
            'course_description' => $row['course_description']
        ];
    }
    $stmt_courses->close();
}

$degree_progress = [];
$to_be_completed_course_details = [];

foreach ($degrees as $index => $degree_id) {
    $degree_name = $degree_info[$degree_id]['name'];
    $degree_type = $degree_info[$degree_id]['type'];
    $degree_label = $degree_labels[$index];

    $required = $required_courses[$degree_id] ?? [];
    $total_required = count($required);
    $completed_required = array_intersect($required, $completedCourses);
    $completed_required_count = count($completed_required);
    $progress_percentage = ($total_required > 0) ? round(($completed_required_count / $total_required) * 100, 2) : 0;

    $degree_progress[$degree_id] = [
        'name' => $degree_name,
        'type' => $degree_type,
        'label' => $degree_label,
        'completed_count' => $completed_required_count,
        'total_required' => $total_required,
        'progress' => $progress_percentage
    ];

    foreach ($required as $course_code) {
        if (!in_array($course_code, $completedCourses) && isset($course_details[$course_code])) {
            $to_be_completed_course_details[$course_code] = $course_details[$course_code];
        }
    }
}

$add_course_error = $_SESSION['add_course_error'] ?? '';
$add_course_success = $_SESSION['add_course_success'] ?? '';
$remove_course_error = $_SESSION['remove_course_error'] ?? '';
$remove_course_success = $_SESSION['remove_course_success'] ?? '';
unset($_SESSION['add_course_error'], $_SESSION['add_course_success'], $_SESSION['remove_course_error'], $_SESSION['remove_course_success']);
?>
