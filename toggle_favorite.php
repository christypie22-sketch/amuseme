<?php
session_start();
include("components/connect.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$ticketId = $data['ticket_id'];
$userId = $_SESSION['user_id'];
$action = $data['action'];

if ($action === 'add') {
    $query = "INSERT INTO favorites (user_id, ticket_id) VALUES ('$userId', '$ticketId')";
} else {
    $query = "DELETE FROM favorites WHERE user_id = '$userId' AND ticket_id = '$ticketId'";
}

if ($conn->query($query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
$conn->close();
?>
