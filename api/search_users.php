<?php
// public/search_users.php

include '../includes/config.php';
include '../includes/functions.php';

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$query = $_GET['query'] ?? '';

if (empty($query)) {
    echo json_encode(["error" => "Empty search query"]);
    exit();
}

// Sanitize the input
$search = "%" . $conn->real_escape_string($query) . "%";

// Prepare the SQL statement
$stmt = $conn->prepare("SELECT student_id, fname, lname, email FROM students WHERE (fname LIKE ? OR lname LIKE ? OR email LIKE ?) AND student_id != ? LIMIT 10");
if ($stmt) {
    $stmt->bind_param("sssi", $search, $search, $search, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        // Determine friendship status
        $friendship_status = 'not_friends';

        // Check if already friends
        $student_id1 = min($user_id, $row['student_id']);
        $student_id2 = max($user_id, $row['student_id']);
        $friend_stmt = $conn->prepare("SELECT * FROM FriendsWith WHERE student_id1 = ? AND student_id2 = ?");
        if ($friend_stmt) {
            $friend_stmt->bind_param("ii", $student_id1, $student_id2);
            $friend_stmt->execute();
            $friend_stmt->store_result();
            if ($friend_stmt->num_rows > 0) {
                $friendship_status = 'friends';
            }
            $friend_stmt->close();
        }

        // Check if a friend request has been sent
        // (Assuming you have a FriendRequests table; if not, skip this)
        // If you don't have friend requests, remove this part
        /*
        $req_stmt = $conn->prepare("SELECT * FROM FriendRequests WHERE sender_id = ? AND receiver_id = ? AND status = 'pending'");
        if ($req_stmt) {
            $req_stmt->bind_param("ii", $user_id, $row['student_id']);
            $req_stmt->execute();
            $req_stmt->store_result();
            if ($req_stmt->num_rows > 0) {
                $friendship_status = 'request_sent';
            }
            $req_stmt->close();
        }
        */

        $users[] = [
            "student_id" => $row['student_id'],
            "name" => htmlspecialchars($row['fname'] . ' ' . $row['lname']),
            "email" => htmlspecialchars($row['email']),
            "status" => $friendship_status
        ];
    }

    echo json_encode(["success" => true, "users" => $users]);
    $stmt->close();
} else {
    echo json_encode(["error" => "Database error"]);
}
?>