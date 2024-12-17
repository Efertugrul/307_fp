<!-- coded by Efe -->

<?php
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}
?>
<div class = "main-content">
  <div class="container my-4">
      <h1 class="text-center mb-4">My Network</h1>

      <div class="card mb-4">
          <div class="card-header">
              <h5>Friends</h5>
          </div>
          <div class="card-body">
              <ul class="list-group" id="friendsList">
              </ul>
          </div>
      </div>

      <div class="card mb-4">
          <div class="card-header">
              <h5>Pending Friend Requests</h5>
          </div>
          <div class="card-body">
              <ul class="list-group" id="pendingReceivedList">
              </ul>
          </div>
      </div>

      <div class="card mb-4">
          <div class="card-header">
              <h5>Send Friend Request</h5>
          </div>
          <div class="card-body">
              <div class="input-group">
                  <input type="text" id="sendFriendInput" class="form-control" placeholder="Enter email to add as friend..." aria-label="Send friend request">
                  <button class="btn btn-primary" id="sendFriendButton" type="button">Send Request</button>
              </div>
              <div id="sendFriendFeedback" class="mt-2"></div>
          </div>
      </div>
  </div>
  <div id="notificationContainer" class="notification-container">
    <div id="notification" class="notification">
      <span id="notificationText"></span>
      <button id="notificationClose" class="notification-close">&times;</button>
    </div>
  </div>



  <div class="modal fade" id="viewScheduleModal" tabindex="-1" aria-labelledby="viewScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="viewScheduleModalLabel">Friend's Schedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
              <label for="friendSemesterSelect" class="form-label">Select Semester:</label>
              <select class="form-select" id="friendSemesterSelect">
                  <option value="">-- Select Semester --</option>
                  <option value="FALL">Fall</option>
                  <option value="WINTER">Winter</option>
                  <option value="SUMMER">Summer</option>
              </select>
          </div>
          

          <div id="scheduleLoading" class="text-center my-3" style="display: none;">
              <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
              </div>
          </div>
          
          <div id="friendScheduleList" class="list-group">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="confirmationModalLabel">Confirm Action</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="confirmationModalBody">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="confirmActionButton">Confirm</button>
        </div>
      </div>
    </div>
  </div>
</div>




<script src="../assets/js/network.js"></script>

<?php
include '../includes/footer.php';
?>
