<?php
// myprogress.php

$pageTitle = "My Progress";
$pageCSS = [
    '../assets/css/global.css',
    '../assets/css/my_progress.css'
];

include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student's major and minor IDs
$sql_degrees = "SELECT major_id, minor_id FROM students WHERE student_id = ?";
$stmt_degrees = $conn->prepare($sql_degrees);
if (!$stmt_degrees) {
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    echo "<div class='container my-5'><div class='alert alert-danger'>An error occurred. Please try again later.</div></div>";
    include '../includes/footer.php';
    exit();
}
$stmt_degrees->bind_param("i", $user_id);
$stmt_degrees->execute();
$stmt_degrees->bind_result($major_id, $minor_id);
$stmt_degrees->fetch();
$stmt_degrees->close();

// Build degrees array with labels
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

// If no degrees are registered
if (empty($degrees)) {
    echo "<div class='container my-5'><div class='alert alert-warning'>You have not registered for any degree programs yet.</div></div>";
    include '../includes/footer.php';
    exit();
}

// Fetch degree names and types
$placeholders = implode(',', array_fill(0, count($degrees), '?'));
$sql_degree_names = "SELECT degree_id, name, type FROM degrees WHERE degree_id IN ($placeholders)";
$stmt_degree_names = $conn->prepare($sql_degree_names);
if (!$stmt_degree_names) {
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    echo "<div class='container my-5'><div class='alert alert-danger'>An error occurred. Please try again later.</div></div>";
    include '../includes/footer.php';
    exit();
}

$types = str_repeat('i', count($degrees));
$stmt_degree_names->bind_param($types, ...$degrees);
$stmt_degree_names->execute();
$res_degree_names = $stmt_degree_names->get_result();
$degree_info = array();
while ($row = $res_degree_names->fetch_assoc()) {
    $degree_info[$row['degree_id']] = [
        'name' => $row['name'],
        'type' => $row['type']
    ];
}
$stmt_degree_names->close();

// Fetch required courses for each degree
$sql_degree_courses = "SELECT degree_id, course_code FROM degree_courses WHERE degree_id IN ($placeholders) AND course_type = 'required'";
if ($major_id == 9) {
    echo "<div class='container my-5'><div class='alert alert-warning'>You haven't declared your degree yet. You can do it from settings.</div></div>";
    include '../includes/footer.php';
    exit();
}

$stmt_degree_courses = $conn->prepare($sql_degree_courses);
if (!$stmt_degree_courses) {
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    echo "<div class='container my-5'><div class='alert alert-danger'>An error occurred. Please try again later.</div></div>";
    include '../includes/footer.php';
    exit();
}
$stmt_degree_courses->bind_param($types, ...$degrees);
$stmt_degree_courses->execute();
$res_degree_courses = $stmt_degree_courses->get_result();
$required_courses = array();
$courses_to_fetch = array();

while ($row = $res_degree_courses->fetch_assoc()) {
    $required_courses[$row['degree_id']][] = $row['course_code'];
    $courses_to_fetch[] = $row['course_code'];
}
$stmt_degree_courses->close();

// Fetch student's completed courses
$sql_completed = "
    SELECT c.course_code
    FROM coursescompleted cc
    JOIN courses c ON cc.course_code = c.course_code
    WHERE cc.student_id = ?
";
$stmt_completed = $conn->prepare($sql_completed);
if (!$stmt_completed) {
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    echo "<div class='container my-5'><div class='alert alert-danger'>An error occurred. Please try again later.</div></div>";
    include '../includes/footer.php';
    exit();
}
$stmt_completed->bind_param("i", $user_id);
$stmt_completed->execute();
$res_completed = $stmt_completed->get_result();
$completedCourses = array();
while ($row = $res_completed->fetch_assoc()) {
    $completedCourses[] = $row['course_code'];
}
$stmt_completed->close();

// Fetch course details for required courses
$course_details = array();
if (!empty($courses_to_fetch)) {
    $placeholders_courses = implode(',', array_fill(0, count($courses_to_fetch), '?'));
    $sql_courses = "SELECT course_code, course_name, course_description FROM courses WHERE course_code IN ($placeholders_courses)";
    $stmt_courses = $conn->prepare($sql_courses);
    if (!$stmt_courses) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        echo "<div class='container my-5'><div class='alert alert-danger'>An error occurred. Please try again later.</div></div>";
        include '../includes/footer.php';
        exit();
    }
    $types_courses = str_repeat('s', count($courses_to_fetch));
    $stmt_courses->bind_param($types_courses, ...$courses_to_fetch);
    $stmt_courses->execute();
    $res_courses = $stmt_courses->get_result();
    while ($row = $res_courses->fetch_assoc()) {
        $course_details[$row['course_code']] = $row;
    }
    $stmt_courses->close();
}

// Prepare data for each degree
$degree_progress = array();
$completed_course_details = array();
$to_be_completed_course_details = array();

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
    
    // Populate completed courses
    foreach ($completed_required as $course_code) {
        $completed_course_details[$course_code] = $course_details[$course_code];
    }
    
    // Populate to be completed courses
    foreach ($required as $course_code) {
        if (!in_array($course_code, $completedCourses)) {
            $to_be_completed_course_details[$course_code] = $course_details[$course_code];
        }
    }
}
?>
    
<div class="main-content myprogress-container">
    <h1 class="mb-4">My Progress</h1>
    <div class="row">
        <!-- Left Column: Progress Charts -->
        <div class="col-md-4 mb-4">
            <?php foreach ($degrees as $index => $degree_id): ?>
                <div class="card text-center mb-4">
                    <div class="card-body">
                        <canvas id="progressChart_<?php echo $degree_id; ?>" width="200" height="200"></canvas>
                        <h3 class="mt-3"><?php echo htmlspecialchars($degree_progress[$degree_id]['label'] . ": " . $degree_progress[$degree_id]['name']); ?></h3>
                        <p>Required Courses Completion: <?php echo $degree_progress[$degree_id]['progress']; ?>%</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Right Column: Courses List -->
        <div class="col-md-8">
            <!-- Completed Courses -->
            <h3>Completed Courses</h3>
            <?php if (!empty($completed_course_details)): ?>
                <div class="accordion mb-4" id="completedCoursesAccordion">
                    <?php foreach ($completed_course_details as $index => $course): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCompleted<?php echo $index; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCompleted<?php echo $index; ?>" aria-expanded="false" aria-controls="collapseCompleted<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                                </button>
                            </h2>
                            <div id="collapseCompleted<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="headingCompleted<?php echo $index; ?>" data-bs-parent="#completedCoursesAccordion">
                                <div class="accordion-body">
                                    <?php 
                                        // Display course description if available
                                        echo htmlspecialchars($course['course_description'] ?? 'No description available.');
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>You haven't completed any required courses yet.</p>
            <?php endif; ?>
    
            <!-- To Be Completed Courses -->
            <h3>To Be Completed</h3>
            <?php if (!empty($to_be_completed_course_details)): ?>
                <div class="accordion" id="toBeCompletedCoursesAccordion">
                    <?php foreach ($to_be_completed_course_details as $index => $course): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingToBeCompleted<?php echo $index; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseToBeCompleted<?php echo $index; ?>" aria-expanded="false" aria-controls="collapseToBeCompleted<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                                </button>
                            </h2>
                            <div id="collapseToBeCompleted<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="headingToBeCompleted<?php echo $index; ?>" data-bs-parent="#toBeCompletedCoursesAccordion">
                                <div class="accordion-body">
                                    <?php 
                                        // Display course description if available
                                        echo htmlspecialchars($course['course_description'] ?? 'No description available.');
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>All required courses completed!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<!-- Chart Initialization Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php foreach ($degrees as $index => $degree_id): ?>
            const ctx_<?php echo $degree_id; ?> = document.getElementById('progressChart_<?php echo $degree_id; ?>').getContext('2d');
            const completed_<?php echo $degree_id; ?> = <?php echo $degree_progress[$degree_id]['completed_count']; ?>;
            const total_<?php echo $degree_id; ?> = <?php echo $degree_progress[$degree_id]['total_required']; ?>;
            const progress_<?php echo $degree_id; ?> = <?php echo $degree_progress[$degree_id]['progress']; ?>;
    
            const data_<?php echo $degree_id; ?> = {
                labels: ['Completed', 'Remaining'],
                datasets: [{
                    data: [completed_<?php echo $degree_id; ?>, total_<?php echo $degree_id; ?> - completed_<?php echo $degree_id; ?>],
                    backgroundColor: ['#4caf50', '#e0e0e0'],
                    borderWidth: 0
                }]
            };
    
            const options_<?php echo $degree_id; ?> = {
                cutout: '70%',
                rotation: -90,
                circumference: 180,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    },
                    // Custom plugin to display percentage in the center
                    beforeDraw: function(chart) {
                        const width = chart.width,
                              height = chart.height,
                              ctx = chart.ctx;
                        ctx.restore();
                        const fontSize = (height / 114).toFixed(2);
                        ctx.font = fontSize + "em sans-serif";
                        ctx.textBaseline = "middle";
        
                        const text = "<?php echo $degree_progress[$degree_id]['progress']; ?>%",
                              textX = Math.round((width - ctx.measureText(text).width) / 2),
                              textY = height / 1.5;
        
                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                }
            };
    
            const progressChart_<?php echo $degree_id; ?> = new Chart(ctx_<?php echo $degree_id; ?>, {
                type: 'doughnut',
                data: data_<?php echo $degree_id; ?>,
                options: options_<?php echo $degree_id; ?>
            });
        <?php endforeach; ?>
    });
</script>

<?php
include '../includes/footer.php';
?>
