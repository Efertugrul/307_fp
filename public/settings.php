<!-- coded by William Rodzen-Staff -->

<?php
$pageTitle = "Settings";
$pageCSS = [
    '../assets/css/global.css',
    '../assets/css/settings.css'
];
$pageJS = [
    '../assets/js/settings.js'
];

include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}




$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT s.major_id, s.minor_id, d_major.name AS major_name, d_minor.name AS minor_name, s.username
    FROM students s
    LEFT JOIN degrees d_major ON s.major_id = d_major.degree_id
    LEFT JOIN degrees d_minor ON s.minor_id = d_minor.degree_id
    WHERE s.student_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user_data = $res->fetch_assoc();
$stmt->close();
?>

<div class="main-content">
    <div class="settings-container">
        <div class="container mt-5">
            <h2 class="text-center mb-4">Settings</h2>

            <?php
            if (isset($_SESSION['settings_success'])) {
                echo '<div class="caution caution-success">' . htmlspecialchars($_SESSION['settings_success']) . '</div>';
                unset($_SESSION['settings_success']);
            }

            if (isset($_SESSION['settings_error'])) {
                echo '<div class="caution caution-danger">' . htmlspecialchars($_SESSION['settings_error']) . '</div>';
                unset($_SESSION['settings_error']);
            }
            ?>

            <form action="../api/update_settings.php" method="POST" class="settings-form">
                <div class="mb-4">
                    <h4>Update Username</h4>
                    <div class="mb-3">
                        <label for="current_username" class="form-label">Current Username:</label>
                        <input type="text" class="form-control" id="current_username" value="<?php echo htmlspecialchars($user_data['username']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="new_username" class="form-label">New Username</label>
                        <input type="text" class="form-control" id="new_username" name="new_username" placeholder="Enter new username (optional)">
                        <div id="usernameHelp" class="form-text">Leave blank to keep your current username. Username must be 3-20 characters long and can include letters, numbers, underscores, and periods.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="major" class="form-label">Update Major <span class="text-danger">*</span></label>
                    <select class="form-select" id="major" name="major_id" required>
                        <?php
                            $sql_majors = "SELECT degree_id, name FROM degrees WHERE type = 'Major' ORDER BY name ASC";
                            $res_majors = $conn->query($sql_majors);
                            while ($major = $res_majors->fetch_assoc()) {
                                $selected = ($major['degree_id'] == $user_data['major_id']) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($major['degree_id']) . "' data-name='" . htmlspecialchars($major['name']) . "' $selected>" . htmlspecialchars($major['name']) . "</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="minor" class="form-label">Update Minor</label>
                    <select class="form-select" id="minor" name="minor_id">
                        <option value="">-- No Minor --</option>
                        <?php
                            $sql_minors = "SELECT degree_id, name FROM degrees WHERE type = 'Minor' ORDER BY name ASC";
                            $res_minors = $conn->query($sql_minors);
                            while ($minor = $res_minors->fetch_assoc()) {
                                $selected = ($minor['degree_id'] == $user_data['minor_id']) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($minor['degree_id']) . "' data-name='" . htmlspecialchars($minor['name']) . "' $selected>" . htmlspecialchars($minor['name']) . "</option>";
                            }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Settings</button>
            </form>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>
