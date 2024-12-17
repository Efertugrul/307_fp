<!-- coded by Jaiten Kang -->

<?php
$pageTitle = "My Progress";
$pageCSS = [
    '../assets/css/global.css',
    '../assets/css/my_progress.css'
];

include '../includes/header.php';

include '../api/my_progress_logic.php';

if (!empty($_SESSION['error_message'])) {
    echo "<div class='container my-5'><div class='caution caution-danger'>" . htmlspecialchars($_SESSION['error_message']) . "</div></div>";
    unset($_SESSION['error_message']);
    include '../includes/footer.php';
    exit();
}

if ($no_degrees_registered) {
    echo "<div class='container my-5'><div class='caution caution-warning'>You have not registered for any degree programs yet.</div></div>";
    include '../includes/footer.php';
    exit();
}
?>

<script>
    const degreeProgressData = <?php echo json_encode($degree_progress); ?>;
    const completedCourses = <?php echo $completedCoursesJSON; ?>;
    const completedCourseDetails = <?php echo json_encode($all_completed_course_details); ?>;
</script>

<div class="main-content myprogress-container">
    <h1 class="mb-4">My Progress</h1>
    <div class="row">
        <div class="col-md-3 mb-3">
            <?php foreach ($degrees as $index => $degree_id): ?>
                <div class="card text-center mb-3">
                    <div class="card-body">
                        <canvas id="progressChart_<?php echo $degree_id; ?>" width="140" height="140"></canvas>
                        <h4 class="mt-2"><?php echo htmlspecialchars($degree_progress[$degree_id]['label'] . ": " . $degree_progress[$degree_id]['name']); ?></h4>
                        <p class="small">Required Courses Completion: <?php echo $degree_progress[$degree_id]['progress']; ?>%</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Add Completed Course</h3>
                </div>
                <div class="card-body">
                    <?php
                        if (!empty($add_course_error)) {
                            echo '<div class="caution caution-danger">' . htmlspecialchars($add_course_error) . '</div>';
                        }
                        if (!empty($add_course_success)) {
                            echo '<div class="caution caution-success">' . htmlspecialchars($add_course_success) . '</div>';
                        }
                        if (!empty($remove_course_error)) {
                            echo '<div class="caution caution-danger">' . htmlspecialchars($remove_course_error) . '</div>';
                        }
                        if (!empty($remove_course_success)) {
                            echo '<div class="caution caution-success">' . htmlspecialchars($remove_course_success) . '</div>';
                        }
                    ?>
                    <form action="../api/handle_progress.php" method="POST" class="add-course-form" autocomplete="off">
                        <div class="mb-3 position-relative">
                            <label for="course_code" class="form-label">Course Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="course_code" name="course_code" placeholder="e.g., COMP-101" required>
                            <div id="autocomplete-list" class="autocomplete-items"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Completed Course</button>
                    </form>
                </div>
            </div>

            <h3>Completed Courses</h3>
            <?php if (!empty($all_completed_course_details)): ?>
                <div class="accordion mb-4" id="completedCoursesAccordion">
                    <?php foreach ($all_completed_course_details as $index => $course): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCompleted<?php echo $index; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCompleted<?php echo $index; ?>" aria-expanded="false" aria-controls="collapseCompleted<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                                </button>
                            </h2>
                            <div id="collapseCompleted<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="headingCompleted<?php echo $index; ?>" data-bs-parent="#completedCoursesAccordion">
                                <div class="accordion-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php 
                                            echo htmlspecialchars($course['course_description'] ?? 'No description available.');
                                        ?>
                                    </div>
                                    <form action="../api/handle_progress.php" method="POST" class="mb-0">
                                        <input type="hidden" name="remove_course_code" value="<?php echo htmlspecialchars($course['course_code']); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>You haven't completed any required courses yet.</p>
            <?php endif; ?>

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

<script>
    const completedCourses = <?php echo $completedCoursesJSON; ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="../assets/js/my_progress.js"></script>

<?php include '../includes/footer.php'; ?>
