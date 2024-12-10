<?php
// public/logout.php
session_start();
include '../includes/config.php';

// Clear remember_token from database
if (isset($_SESSION['user_id'])) {
    $student_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("UPDATE students SET remember_token=NULL WHERE student_id=?");
    if ($stmt) {
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Destroy session
session_unset();
session_destroy();

// Clear remember_me cookie
setcookie("remember_me", "", time() - 3600, "/");

// Redirect to login page with a success message
session_start();
$_SESSION['success'] = "You have been logged out successfully.";
header("Location: index.php");
exit();
?>
