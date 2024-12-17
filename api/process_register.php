<!-- coded by Efe -->
<?php
session_start();
include '../includes/config.php';
include '../includes/functions.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = strtolower(sanitizeInput($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $major_id = isset($_POST['major_id']) ? intval($_POST['major_id']) : 0;
    $minor_id = isset($_POST['minor_id']) ? intval($_POST['minor_id']) : null;

    $req_ext = '@mail.mcgill.ca';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($major_id)) {
        $_SESSION['error'] = "All required fields must be filled out.";
        header("Location: ../public/register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: ../public/register.php");
        exit();
    }

    if (substr($email, -strlen($req_ext)) !== $req_ext) {
        $_SESSION['error'] = "Email must end with " . htmlspecialchars($req_ext);
        header("Location: ../public/register.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT username FROM students WHERE username = ? LIMIT 1");
    if (!$stmt) {
        $_SESSION['error'] = "Database error: " . $conn->error;
        header("Location: ../public/register.php");
        exit();
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Username already taken. Please choose another.";
        $stmt->close();
        $conn->close();
        header("Location: ../public/register.php");
        exit();
    }
    $stmt->close();

    $stmt_email = $conn->prepare("SELECT email FROM students WHERE email = ? LIMIT 1");
    if (!$stmt_email) {
        $_SESSION['error'] = "Database error: " . $conn->error;
        header("Location: ../public/register.php");
        exit();
    }
    $stmt_email->bind_param("s", $email);
    $stmt_email->execute();
    $stmt_email->store_result();

    if ($stmt_email->num_rows > 0) {
        $_SESSION['error'] = "An account with this email already exists.";
        $stmt_email->close();
        $conn->close();
        header("Location: ../public/register.php");
        exit();
    }
    $stmt_email->close();

    if (strlen($password) < 8) {
        $_SESSION['error'] = "Password must be at least 8 characters long.";
        header("Location: ../public/register.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../public/register.php");
        exit();
    }

    $stmt_major = $conn->prepare("SELECT name FROM degrees WHERE degree_id = ? AND type = 'Major' LIMIT 1");
    if ($stmt_major) {
        $stmt_major->bind_param("i", $major_id);
        $stmt_major->execute();
        $res_major = $stmt_major->get_result();
        if ($res_major->num_rows === 0) {
            $_SESSION['error'] = "Selected Major is invalid.";
            $stmt_major->close();
            header("Location: ../public/register.php");
            exit();
        }
        $major_data = $res_major->fetch_assoc();
        $major_name = $major_data['name'];
        $stmt_major->close();
    } else {
        $_SESSION['error'] = "Database error: " . $conn->error;
        header("Location: ../public/register.php");
        exit();
    }

    if (!empty($minor_id)) {
        $stmt_minor = $conn->prepare("SELECT name FROM degrees WHERE degree_id = ? AND type = 'Minor' LIMIT 1");
        if ($stmt_minor) {
            $stmt_minor->bind_param("i", $minor_id);
            $stmt_minor->execute();
            $res_minor = $stmt_minor->get_result();
            if ($res_minor->num_rows === 0) {
                $_SESSION['error'] = "Selected Minor is invalid.";
                $stmt_minor->close();
                header("Location: ../public/register.php");
                exit();
            }
            $minor_data = $res_minor->fetch_assoc();
            $minor_name = $minor_data['name'];
            $stmt_minor->close();

            if (strcasecmp($major_name, $minor_name) === 0) { // Case-insensitive comparison
                $_SESSION['error'] = "Minor cannot be the same as Major.";
                header("Location: ../public/register.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Database error: " . $conn->error;
            header("Location: ../public/register.php");
            exit();
        }
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $verification_code = generateVerificationCode(); 


    $subject = "Verify Your Advanced Schedule Builder Account";
    $verification_message = "Hello $username,\n\nYour verification code is: $verification_code\n\nPlease enter this 6-digit code on the verification page to activate your account.\n\nIf you did not request this, please ignore this email.";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                           
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                  
        $mail->Username   = 'efertugrul6@gmail.com';       
        $mail->Password   = 'btaf jkay gddz pcwq';            
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     
        $mail->Port       = 587;                                

        $mail->setFrom('noreply@advsb.com', 'Advanced Schedule Builder');
        $mail->addAddress($email, $username);                      

        $mail->isHTML(false);                                       
        $mail->Subject = $subject;
        $mail->Body    = $verification_message;

        $mail->send();
        $mail_sent = true;
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to send verification email. Mailer Error: {$mail->ErrorInfo}";
        $conn->close();
        header("Location: ../public/register.php");
        exit();
    }

    if ($mail_sent) {
        $stmt_insert = null;
        if (empty($minor_id)) {
            $stmt_insert = $conn->prepare("INSERT INTO students (username, email, password_hash, verification_code, major_id, is_verified) VALUES (?, ?, ?, ?, ?, 0)");
            $stmt_insert->bind_param("sssii", $username, $email, $password_hash, $verification_code, $major_id);
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO students (username, email, password_hash, verification_code, major_id, minor_id, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)");
            $stmt_insert->bind_param("sssiii", $username, $email, $password_hash, $verification_code, $major_id, $minor_id);
        }

        if ($stmt_insert->execute()) {
            $_SESSION['success'] = "Registration successful! A 6-digit verification code has been sent to your email. Please verify your account.";
        } else {
            $_SESSION['error'] = "Registration failed due to a database error. Please try again.";
        }
        $stmt_insert->close();
    }

    $conn->close();
    header("Location: ../public/register.php");
    exit();
} else {
    header("Location: ../public/register.php");
    exit();
}
?>
