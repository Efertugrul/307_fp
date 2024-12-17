<!-- coded by Efe -->

<?php
$pageTitle = "Register";
$pageCSS = [
    '../assets/css/global.css',
    '../assets/css/register.css'
];
$pageJS = [
    '../assets/js/register.js'
];

include '../includes/header.php';
?>

<div class="main-content">
  <div class="container mt-5">
    <h2 class="text-center mb-4">Register</h2>

    <?php
    if (isset($_SESSION['success'])) {
      echo '<div class="caution caution-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
      echo '<script type="text/javascript">
              document.addEventListener("DOMContentLoaded", function() {
                  var myModal = new bootstrap.Modal(document.getElementById("verificationModal"), {});
                  myModal.show();
              });
            </script>';
      unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
      echo '<div class="caution caution-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
      unset($_SESSION['error']);
    }
    ?>

    <?php if (!isset($_SESSION['reset_code_sent'])): ?>
      <form action="../api/process_register.php" method="POST" class="register-form mb-5">
        <div class="mb-3">
          <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="username" name="username" placeholder="Create a unique username" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">School Email <span class="text-danger">*</span></label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Enter your @mail.mcgill.ca email" required>
        </div>
        <div class="mb-3">
          <label for="major" class="form-label">Select Major <span class="text-danger">*</span></label>
          <select class="form-select" id="major" name="major_id" required>
              <option value="">-- Select Major --</option>
              <?php
                  $sql_majors = "SELECT degree_id, name FROM degrees WHERE type = 'Major' ORDER BY name ASC";
                  $res_majors = $conn->query($sql_majors);
                  while ($major = $res_majors->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($major['degree_id']) . "' data-name='" . htmlspecialchars($major['name']) . "'>" . htmlspecialchars($major['name']) . "</option>";
                  }
              ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="minor" class="form-label">Select Minor</label>
          <select class="form-select" id="minor" name="minor_id">
              <option value="">-- Select Minor (Optional) --</option>
              <?php
                  $sql_minors = "SELECT degree_id, name FROM degrees WHERE type = 'Minor' ORDER BY name ASC";
                  $res_minors = $conn->query($sql_minors);
                  while ($minor = $res_minors->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($minor['degree_id']) . "' data-name='" . htmlspecialchars($minor['name']) . "'>" . htmlspecialchars($minor['name']) . "</option>";
                  }
              ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Create a strong password" required>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
        <div class="text-center mt-3">
          <a href="login.php" class="link">Already have an account? Login here</a>
        </div>
      </form>
    <?php endif; ?>


    <div class="text-center mb-5">
      <button type="button" class="verify-btn btn btn-secondary" data-bs-toggle="modal" data-bs-target="#verificationModal">
        Already have your verification code? Verify here
      </button>
    </div>
  </div>

  <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="../api/verify_code.php" method="POST" class="verify-form">
          <div class="modal-header">
            <h5 class="modal-title" id="verificationModalLabel">Verify Your Account</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Please enter your 6-digit verification code sent to your email to activate your account.</p>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="caution caution-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>
            <div class="mb-3">
                <label for="verification_code" class="form-label">Verification Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="verification_code" name="verification_code" maxlength="6" placeholder="123456" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Verify Account</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
include '../includes/footer.php';
?>
