<?php
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'ticket_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all questions from the chat_responses table
$query = "SELECT question FROM chat_responses";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<button class="question-btn">' . htmlspecialchars($row['question']) . '</button>';
    }
} else {
    echo '<p>No predefined questions found.</p>';
}

$conn->close();
