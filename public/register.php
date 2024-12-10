<?php
$pageTitle = "Register";
$pageCSS = [
    '../assets/css/register.css'
];
$pageJS = [
  '../assets/css/global.css',
  '../assets/js/register.js'
];

include '../includes/header.php';
?>

<div class="main-content">
  <div class="container mt-5">
    <h2 class="text-center mb-4">Register</h2>

    <?php
    if (isset($_SESSION['success'])) {
      echo '<script type="text/javascript">',
          'document.addEventListener("DOMContentLoaded", function() {',
            'var myModal = new bootstrap.Modal(document.getElementById("verificationModal"), {});',
            'myModal.show();',
            '});',
            '</script>';
      echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
      unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
      echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
      unset($_SESSION['error']);
    }
    ?>

    <form action="../api/process_register.php" method="POST" class="register-form mb-5">
      <div class="mb-3">
        <label for="student_id" class="form-label">Student ID</label>
        <input type="text" class="form-control" id="student_id" name="student_id" placeholder="Enter your Student ID" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">School Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your school email" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
      </div>
      <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
      </div>
      <button type="submit" class="btn btn-primary">Register</button>
      <div class="text-center mt-3">
        <a href="login.php" class="link">Already have an account? Login here</a>
      </div>
    </form>
  </div>

  <!-- Verification Modal -->
  <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="../api/verify_code.php" method="POST" class="verify-form">
          <div class="modal-header">
            <h5 class="modal-title" id="verificationModalLabel">Verify Your Account</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>A 6-digit verification code has been sent to your email. Please enter it below to activate your account.</p>
            <div class="mb-3">
                <label for="verification_code" class="form-label">Verification Code</label>
                <input type="text" class="form-control" id="verification_code" name="verification_code" maxlength="6" placeholder="123456" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Verify Account</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

<?php
include '../includes/footer.php';
?>
