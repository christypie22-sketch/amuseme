<?php
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'ticket_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the AJAX request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userMessage = $conn->real_escape_string($_POST['message']);

    // Search for a matching question in the database
    $query = "SELECT answer FROM chat_responses WHERE question LIKE '%$userMessage%' LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Fetch the answer
        $row = $result->fetch_assoc();
        echo $row['answer'];
    } else {
        // No matching question found, respond with a default message
        echo "I'm sorry, I don't have an answer to that question. Try asking something else.";
    }
}

$conn->close();
