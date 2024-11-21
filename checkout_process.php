<?php
include('components/connect.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['complete_payment']) && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $total_price = $_POST['total_price'];
    $reference_no = $_POST['reference_no'];

    // Fetch user ID
    $user_query = "SELECT id FROM users WHERE username='$username' LIMIT 1";
    $user_result = mysqli_query($db, $user_query);
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user_data = mysqli_fetch_assoc($user_result);
        $user_id = $user_data['id'];

        // Create a new order with reference number
        $order_query = "INSERT INTO orders (user_id, total_amount, reference_no, order_date) 
                        VALUES ('$user_id', '$total_price', '$reference_no', NOW())";
        $order_result = mysqli_query($db, $order_query);

        if ($order_result) {
            // Clear the cart
            $clear_cart_query = "DELETE FROM cart WHERE user_id='$user_id'";
            mysqli_query($db, $clear_cart_query);

            // Redirect to success page
            $_SESSION['success_message'] = "Checkout successful!";
            header("Location: order_success.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to process your order. Please try again.";
            header("Location: profile.php");
            exit();
        }
    }
} else {
    header("Location: profile.php");
    exit();
}
?>
