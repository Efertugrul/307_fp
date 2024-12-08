<?php
// public/remove_friend.php

include '../includes/config.php';
include '../includes/functions.php';

session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    http_response_code(403);
    echo json_encode(["error"=>"Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$friend_id = $_GET['friend_id'] ?? '';

if (!ctype_digit($friend_id)){
    echo json_encode(["error"=>"Invalid friend_id"]);
    exit();
}

$friend_id = (int)$friend_id;

// Determine the order
$student_id1 = min($user_id, $friend_id);
$student_id2 = max($user_id, $friend_id);

// Check if they are friends
$stmt = $conn->prepare("SELECT * FROM FriendsWith WHERE student_id1 = ? AND student_id2 = ?");
$stmt->bind_param("ii", $student_id1, $student_id2);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    echo json_encode(["error" => "You are not friends with this user"]);
    exit();
}
$stmt->close();

// Delete the friendship
$del = $conn->prepare("DELETE FROM FriendsWith WHERE student_id1 = ? AND student_id2 = ?");
$del->bind_param("ii", $student_id1, $student_id2);
$del->execute();

if ($del->affected_rows > 0) {
    echo json_encode(["success"=>true, "message"=>"Friend removed successfully"]);
} else {
    echo json_encode(["error"=>"Failed to remove friend"]);
}
$del->close();
?>
