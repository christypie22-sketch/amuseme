<?php
// Start session to access user_id
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming user_id and order_id are stored in session after login
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : null; // Ensure order_id is passed and is an integer
$current_ticket_id = isset($_POST['current_ticket_id']) ? intval($_POST['current_ticket_id']) : null;

// Handle feedback submission
$feedback_message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedbackText'], $_POST['rating'], $_POST['order_id'], $_POST['current_ticket_id']) && $user_id !== null) {
    // Retrieve and validate form data
    $rating = intval($_POST['rating']); // Get rating value (ensure it’s between 1 and 5)
    $feedback = $conn->real_escape_string(trim($_POST['feedbackText'])); // Escape feedback input for security
    
    // Validate rating before inserting
    if ($rating >= 1 && $rating <= 5) {
        // Insert feedback into feedback_ride table
        $sql = "INSERT INTO feedback_ride (user_id, rating, feedback, ticket_id) VALUES ($user_id, $rating, '$feedback', $current_ticket_id)";
        if ($conn->query($sql)) {
            // Update the is_feedback_done status in the orders table
            $update_sql = "UPDATE orders SET is_feedback_done = TRUE WHERE id = $order_id";
            if ($conn->query($update_sql)) {
                $feedback_message = "Thank you for your feedback! $current_ticket_id";
                header("Location: user_profile.php?orderId=" . urlencode($current_ticket_id)); 
            } else {
                $feedback_message = "Feedback submitted, but there was an error updating the order status.";
            }
        } else {
            $feedback_message = "Error: Could not submit feedback.";
        }
    } else {
        $feedback_message = "Invalid rating. Please select a rating between 1 and 5.";
    }
} elseif ($user_id === null && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $feedback_message = "You must be logged in to submit feedback.";
}

$conn->close();

// header("Location: user_profile.php?feedbackMessage=" . urlencode($feedback_message));
exit();

?>