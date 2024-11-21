<?php
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = $conn->real_escape_string($_POST['ticket_id']);
    $ticket_price = $conn->real_escape_string($_POST['price']);
    
    // Assuming you have a user ID (for example, stored in session)
    $user_id = $_SESSION['user_id'];  // Replace this with the actual user ID logic

    // Check if the ticket is already in the cart
    $checkSql = "SELECT * FROM cart WHERE user_id = '$user_id' AND ticket_id = '$ticket_id'";
    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        // Ticket is already in the cart
        echo "<script>
                alert('This ticket is already in your cart!');
                window.location.href = 'home.php';
            </script>";
    } else {
        // Insert into cart
        $sql = "INSERT INTO cart (user_id, ticket_id, price) VALUES ('$user_id', '$ticket_id', '$ticket_price')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('Ticket added to cart successfully!');
                    window.location.href = 'home.php';
                </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
