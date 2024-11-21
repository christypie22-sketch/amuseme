<?php
include('components/connect.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the delete request is received
if (isset($_POST['delete_ticket'])) {
    $ticket_id = $_POST['ticket_id'];
    
    // Fetch the user ID from the session
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $user_query = "SELECT id FROM users WHERE email='$email' LIMIT 1";
        $user_result = mysqli_query($db, $user_query);
        
        if ($user_result && mysqli_num_rows($user_result) > 0) {
            $user_data = mysqli_fetch_assoc($user_result);
            $user_id = $user_data['id'];

            // Delete the ticket from the cart
            $delete_query = "DELETE FROM cart WHERE user_id='$user_id' AND ticket_id='$ticket_id'";
            if (mysqli_query($db, $delete_query)) {
                echo "<script>
                        alert('Ticket deleted successfully!');
                        window.location.href = 'user_profile.php';
                    </script>";
            } else {
                echo "Error deleting ticket: " . mysqli_error($db);
            }
            
            $delete_query = "DELETE FROM favorites WHERE user_id='$user_id' AND ticket_id='$ticket_id'";
            if (mysqli_query($db, $delete_query)) {
                echo "<script>
                        alert('Ticket deleted successfully!');
                        window.location.href = 'user_profile.php';
                    </script>";
            } else {
                echo "Error deleting ticket: " . mysqli_error($db);
            }

        } else {
            echo "User not found.";
        }
    } else {
        echo "Please log in to delete a ticket.";
    }
} else {
    // Redirect if accessed without a delete request
    header('Location: profile.php');
    exit();
}
?>
