<?php
include '../includes/header.php'; 
?>
<div class="container my-4">
    <h1 class="text-center mb-4">My Network</h1>

    <!-- Friends List Section -->
    <!-- Friends List Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Friends</h5>
        </div>
        <div class="card-body">
            <ul class="list-group" id="friendsList">
                <!-- Friends will be dynamically loaded here -->
            </ul>
        </div>
    </div>


    <!-- Pending Friend Requests Received -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Pending Friend Requests</h5>
        </div>
        <div class="card-body">
            <ul class="list-group" id="pendingReceivedList">
                <!-- Pending received requests will be loaded here -->
            </ul>
        </div>
    </div>

    <!-- Send Friend Request Section -->
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

<!-- Toast Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
  <div id="notificationToast" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastBody">
        <!-- Toast message will be injected here -->
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<!-- Modal to View Friend's Schedule -->
<div class="modal fade" id="viewScheduleModal" tabindex="-1" aria-labelledby="viewScheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="viewScheduleModalLabel">Friend's Schedule</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Semester Selection -->
        <div class="mb-3">
            <label for="friendSemesterSelect" class="form-label">Select Semester:</label>
            <select class="form-select" id="friendSemesterSelect">
                <option value="">-- Select Semester --</option>
                <option value="FALL">Fall</option>
                <option value="WINTER">Winter</option>
                <option value="SUMMER">Summer</option>
            </select>
        </div>
        <!-- Schedule Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="friendScheduleTable">
                <thead class="table-dark">
                    <tr>
                        <th>Time</th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $hours = range(8, 18);
                    foreach ($hours as $h) {
                        $timeLabel = sprintf("%02d:00", $h);
                        echo "<tr>";
                        echo "<td class='time-cell'>$timeLabel</td>";
                        echo "<td class='Mon-$h day-cell'></td>";
                        echo "<td class='Tue-$h day-cell'></td>";
                        echo "<td class='Wed-$h day-cell'></td>";
                        echo "<td class='Thu-$h day-cell'></td>";
                        echo "<td class='Fri-$h day-cell'></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="confirmationModalLabel">Confirm Action</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="confirmationModalBody">
        <!-- Confirmation message will be injected here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmActionButton">Confirm</button>
      </div>
    </div>
  </div>
</div>


<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- External Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAc_2sgP5U-SRL68dw2XrUVq2ptSBl-3JI"></script>

<!-- Custom JavaScript -->
<script src="../assets/js/network.js"></script>

<?php
include '../includes/footer.php';
?>
