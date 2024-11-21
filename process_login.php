<?php
session_start();
include("components/connect.php");

// Assuming you have a database connection set up here...

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // Consider using password_hash and password_verify for security

    // Your login logic to verify username and password
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'"; // Example, modify as needed
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User is logged in
        $_SESSION['user_id'] = $result->fetch_assoc()['id']; // Assuming you have an 'id' field

        // Redirect to the specified page after login
        $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php'; // Set a default if not provided
        header("Location: $redirect");
        exit;
    } else {
        echo "Invalid username or password.";
    }
}
$conn->close();
?>
