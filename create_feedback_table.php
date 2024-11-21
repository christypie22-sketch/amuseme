<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL statement to create the feedback table
$sql = "CREATE TABLE feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),  -- Optional: Enforces rating range
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)  -- Assuming you have a users table
)";

// Execute the query and check for errors
if ($conn->query($sql) === TRUE) {
    echo "Table 'feedback' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

// Close the connection
$conn->close();
?>
