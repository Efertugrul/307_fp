<?php
session_start();
include '../includes/config.php';
include '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['settings_error'] = "You must be logged in to update your settings.";
    header("Location: ../public/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $major_id = isset($_POST['major_id']) ? intval($_POST['major_id']) : 0;
    $minor_id = isset($_POST['minor_id']) ? $_POST['minor_id'] : NULL;
    $new_username = isset($_POST['new_username']) ? trim($_POST['new_username']) : '';

    // Initialize error array
    $errors = [];

    // Validate New Username if provided
    $username_to_update = false; // Flag to check if username needs to be updated
    if (!empty($new_username)) {
        $username_to_update = true;

        // Check username length
        if (strlen($new_username) < 3 || strlen($new_username) > 20) {
            $errors[] = "Username must be between 3 and 20 characters long.";
        }

        // Check allowed characters (letters, numbers, underscores, periods)
        if (!preg_match('/^[A-Za-z0-9_.]+$/', $new_username)) {
            $errors[] = "Username can only contain letters, numbers, underscores, and periods.";
        }

        // Check if username is already taken by another user
        if (empty($errors)) { // Proceed only if no previous errors
            $stmt_username = $conn->prepare("SELECT student_id FROM students WHERE username = ? AND student_id != ?");
            if (!$stmt_username) {
                $errors[] = "Database error: " . $conn->error;
            } else {
                $stmt_username->bind_param("si", $new_username, $user_id);
                $stmt_username->execute();
                $stmt_username->store_result();
                if ($stmt_username->num_rows > 0) {
                    $errors[] = "The username '{$new_username}' is already taken. Please choose another.";
                }
                $stmt_username->close();
            }
        }
    }

    // Validate Major
    if (empty($major_id)) {
        $errors[] = "Major selection is required.";
    } else {
        $stmt_major = $conn->prepare("SELECT degree_id FROM degrees WHERE degree_id = ? AND type = 'Major'");
        if (!$stmt_major) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt_major->bind_param("i", $major_id);
            $stmt_major->execute();
            $res_major = $stmt_major->get_result();
            if ($res_major->num_rows === 0) {
                $errors[] = "Selected Major is invalid.";
            }
            $stmt_major->close();
        }
    }

    // Validate Minor if provided
    if (!empty($minor_id)) {
        $minor_id = intval($minor_id);
        $stmt_minor = $conn->prepare("SELECT degree_id FROM degrees WHERE degree_id = ? AND type = 'Minor'");
        if (!$stmt_minor) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt_minor->bind_param("i", $minor_id);
            $stmt_minor->execute();
            $res_minor = $stmt_minor->get_result();
            if ($res_minor->num_rows === 0) {
                $errors[] = "Selected Minor is invalid.";
            }
            $stmt_minor->close();
        }
    } else {
        $minor_id = NULL; // Explicitly set to NULL if no minor is selected
    }

    if (!empty($errors)) {
        $_SESSION['settings_error'] = implode(' ', $errors);
        header("Location: ../public/settings.php");
        exit();
    }

    // Begin Transaction
    $conn->begin_transaction();

    try {
        // Update the student's major, minor, and username if applicable
        if ($username_to_update) {
            $stmt_update = $conn->prepare("UPDATE students SET major_id = ?, minor_id = ?, username = ? WHERE student_id = ?");
            if (!$stmt_update) {
                throw new Exception("Database error: " . $conn->error);
            }

            $stmt_update->bind_param("iisi", $major_id, $minor_id, $new_username, $user_id);
        } else {
            $stmt_update = $conn->prepare("UPDATE students SET major_id = ?, minor_id = ? WHERE student_id = ?");
            if (!$stmt_update) {
                throw new Exception("Database error: " . $conn->error);
            }

            $stmt_update->bind_param("iii", $major_id, $minor_id, $user_id);
        }

        if (!$stmt_update->execute()) {
            throw new Exception("Failed to update settings: " . $stmt_update->error);
        }

        $stmt_update->close();

        // Commit Transaction
        $conn->commit();

        // Set Success Message
        $_SESSION['settings_success'] = "Settings successfully updated.";

        header("Location: ../public/settings.php");
        exit();
    } catch (Exception $e) {
        // Rollback Transaction
        $conn->rollback();
        error_log("Error updating settings: " . $e->getMessage());
        $_SESSION['settings_error'] = "Failed to update settings. Please try again later.";
        header("Location: ../public/settings.php");
        exit();
    }
} else {
    header("Location: ../public/settings.php");
    exit();
}
?>
