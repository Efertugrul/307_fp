<!-- coded by Efe -->
<?php
session_start(); 

include_once '../includes/config.php';
include_once '../includes/functions.php';

require '../vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $_SESSION['fp_error'] = "Invalid request method.";
    header("Location: ../public/forgot_password.php");
    exit();
}

$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';

if (empty($email)) {
    $_SESSION['fp_error'] = "Please enter your registered email.";
    header("Location: ../public/forgot_password.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['fp_error'] = "Please enter a valid email address.";
    header("Location: ../public/forgot_password.php");
    exit();
}

$stmt = $conn->prepare("SELECT student_id, email FROM students WHERE email = ? LIMIT 1");
if (!$stmt) {
    $_SESSION['fp_error'] = "Database error: " . $conn->error;
    header("Location: ../public/forgot_password.php");
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $_SESSION['fp_error'] = "No account found with that email.";
    $stmt->close();
    $conn->close();
    header("Location: ../public/forgot_password.php");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

$user_id = $user['student_id'];
$user_email = $user['email'];

$reset_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
$reset_expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

$stmt_update = $conn->prepare("UPDATE students SET password_reset_code = ?, password_reset_expires = ? WHERE student_id = ?");
if (!$stmt_update) {
    $_SESSION['fp_error'] = "Database error: " . $conn->error;
    header("Location: ../public/forgot_password.php");
    exit();
}

$stmt_update->bind_param("ssi", $reset_code, $reset_expires, $user_id);
if (!$stmt_update->execute()) {
    $_SESSION['fp_error'] = "Failed to set reset code. Please try again.";
    $stmt_update->close();
    $conn->close();
    header("Location: ../public/forgot_password.php");
    exit();
}
$stmt_update->close();
$conn->close();

$subject = "Your Password Reset Code";
$message = "
    <p>Hello,</p>
    <p>You have requested to reset your password. Please use the 6-digit code below to reset your password. This code is valid for 1 hour.</p>
    <h2>{$reset_code}</h2>
    <p>If you did not request a password reset, please ignore this email.</p>
    <p>Thank you.</p>
";



$mail = new PHPMailer(true);

try {
    $mail->isSMTP();                                          
    $mail->Host       = 'smtp.gmail.com';                  
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = 'efertugrul6@gmail.com';              
    $mail->Password   = 'btaf jkay gddz pcwq';               
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      
    $mail->Port       = 587;                                 

    $mail->setFrom('no-reply@advsb.com', 'Advanced Schedule Builder');
    $mail->addAddress($user_email);                            

    $mail->isHTML(true);                                       
    $mail->Subject = $subject;
    $mail->Body    = $message;


    $mail->send();

    $_SESSION['reset_code_sent'] = true;
    $_SESSION['fp_success'] = "A password reset code has been sent to your email.";
    header("Location: ../public/forgot_password.php");
    exit();
} catch (Exception $e) {
    $_SESSION['fp_error'] = "Failed to send reset email. Mailer Error: {$mail->ErrorInfo}";
    header("Location: ../public/forgot_password.php");
    exit();
}
?>
