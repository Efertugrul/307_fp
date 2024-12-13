<?php
session_start();

include_once 'config.php';
include_once 'functions.php';

function initializeSession($conn) {
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
        $stmt = $conn->prepare("SELECT student_id FROM students WHERE remember_token = ?");
        if ($stmt) {
            $stmt->bind_param("s", $_COOKIE['remember_me']);
            $stmt->execute();
            $stmt->bind_result($student_id);
            if ($stmt->fetch()) {
                $_SESSION['user_id'] = $student_id;
                // Regenerate remember_me cookie for security
                setcookie('remember_me', $_COOKIE['remember_me'], time() + (86400 * 30), "/", "", isset($_SERVER['HTTPS']), true);
            }
            $stmt->close();
        }
    }
}

function getUserDetails($conn, $user_id) {
    $stmt = $conn->prepare("SELECT username FROM students WHERE student_id = ? LIMIT 1");
    $details = ['username' => ''];
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($details['username']);
        $stmt->fetch();
        $stmt->close();
    }
    return $details;
}

initializeSession($conn);
$user_details = isset($_SESSION['user_id']) ? getUserDetails($conn, $_SESSION['user_id']) : null;
$current_page = basename($_SERVER['PHP_SELF']);

if ($current_page === 'index.php' && isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Advanced Schedule Builder'; ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- AOS (Animate On Scroll) CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    
    <!-- Global Custom CSS -->
    <link href="../assets/css/global.css" rel="stylesheet">

    <?php
        // Include page-specific CSS if available
        if (isset($pageCSS) && is_array($pageCSS)) {
            foreach ($pageCSS as $css) {
                echo '<link href="' . htmlspecialchars($css) . '" rel="stylesheet">';
            }
        }
    ?>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="../assets/images/logo.png" alt="Logo" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-4 text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($user_details['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="myprogress.php">My Progress</a></li>
                            <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                            <li><a class="dropdown-item" href="network.php">Network</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../api/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white px-4" href="login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
