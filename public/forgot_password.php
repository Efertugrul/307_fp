<!-- coded by Efe -->
<?php
include_once '../includes/functions.php';

$pageTitle = "Forgot Password";
$pageCSS = [
    '../assets/css/global.css',
    '../assets/css/login.css'
];
$pageJS = [
    '../assets/js/login.js'
];

include_once '../includes/header.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$reset_code_sent = isset($_SESSION['reset_code_sent']) && $_SESSION['reset_code_sent'] === true;
unset($_SESSION['reset_code_sent']); 

$step = 1; 

if (isset($_POST['verify_code'])) {
    $reset_code = isset($_POST['reset_code']) ? sanitizeInput($_POST['reset_code']) : '';

    if (!preg_match('/^\d{6}$/', $reset_code)) {
        $_SESSION['fp_error'] = "Invalid reset code format. Please enter a 6-digit code.";
        header("Location: ../public/forgot_password.php");
        exit();
    }

    include_once '../includes/config.php'; 

    $stmt = $conn->prepare("SELECT student_id FROM students WHERE password_reset_code = ? AND password_reset_expires >= NOW() LIMIT 1");
    if (!$stmt) {
        $_SESSION['fp_error'] = "Database error: " . $conn->error;
        header("Location: ../public/forgot_password.php");
        exit();
    }

    $stmt->bind_param("s", $reset_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['fp_error'] = "Invalid or expired reset code.";
        $stmt->close();
        $conn->close();
        header("Location: ../public/forgot_password.php");
        exit();
    }

    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    $_SESSION['reset_user_id'] = $user['student_id'];

    $step = 2;
}
?>

<?php if (!$reset_code_sent && $step == 1): ?>
    <div class="main-content container mt-5">
        <h2 class="text-center mb-4">Forgot Password</h2>

        <?php
        if (isset($_SESSION['fp_success'])) {
            echo '<div class="caution caution-success">' . htmlspecialchars($_SESSION['fp_success']) . '</div>';
            unset($_SESSION['fp_success']);
        }

        if (isset($_SESSION['fp_error'])) {
            echo '<div class="caution caution-danger">' . htmlspecialchars($_SESSION['fp_error']) . '</div>';
            unset($_SESSION['fp_error']);
        }
        ?>

        <form action="../api/request_password_reset.php" method="POST" class="forgot-password-form mb-5">
            <div class="mb-3">
                <label for="email" class="form-label">Registered Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your registered email" required>
            </div>
            <button type="submit" class="btn btn-primary">Send Reset Code</button>
            <div class="text-center mt-3">
                <a href="login.php" class="link">Back to Login</a>
            </div>
        </form>
    </div>
<?php elseif ($reset_code_sent || $step == 1): ?>
    <div class="main-content container mt-5">
        <h2 class="text-center mb-4">Verify Reset Code</h2>

        <?php
        if (isset($_SESSION['fp_success'])) {
            echo '<div class="caution caution-success">' . htmlspecialchars($_SESSION['fp_success']) . '</div>';
            unset($_SESSION['fp_success']);
        }

        if (isset($_SESSION['fp_error'])) {
            echo '<div class="caution caution-danger">' . htmlspecialchars($_SESSION['fp_error']) . '</div>';
            unset($_SESSION['fp_error']);
        }
        ?>

        <form action="" method="POST" class="verify-code-form mb-5">
            <div class="mb-3">
                <label for="reset_code" class="form-label">Reset Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="reset_code" name="reset_code" placeholder="Enter the 6-digit code sent to your email" required maxlength="6">
            </div>
            <button type="submit" name="verify_code" class="btn btn-primary">Verify Code</button>
            <div class="text-center mt-3">
                <a href="forgot_password.php" class="link">Back to Forgot Password</a>
            </div>
        </form>
    </div>
<?php elseif ($step == 2): ?>
    <?php
    header("Location: reset_password.php");
    exit();
    ?>
<?php endif; ?>

<?php
include_once '../includes/footer.php';
?>
