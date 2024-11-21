<?php
session_start();
include('components/connect.php'); 

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['ticket_id'])) {
    if (isset($_SESSION['user_id'])) {
        $ticket_id = $_POST['ticket_id'];
        $user_id = $_SESSION['user_id'];

        // Check if the ticket is already in favorites
        $check_favorite = "SELECT * FROM favorites WHERE user_id='$user_id' AND ticket_id='$ticket_id'";
        $check_result = mysqli_query($conn, $check_favorite);
        
        if (mysqli_num_rows($check_result) == 0) {
            // Insert ticket into favorites
            $insert_favorite = "INSERT INTO favorites (user_id, ticket_id) VALUES ('$user_id', '$ticket_id')";
            mysqli_query($conn, $insert_favorite);
            echo "<script>
                alert('Ticket added to favorites.');
                window.location.href = 'home.php';
            </script>";
        } else {
            echo "<script>
                alert('Ticket is already in favorites.'); 
                window.location.href = 'home.php';
            </script>";
        }
    } else {
        echo "
                <script>alert('Please log in to add favorites.');</script>
            ";
    }
        exit();
}
?>
