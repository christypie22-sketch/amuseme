<?php
include('components/connect.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['customer_name']) && isset($_POST['reference_no']) && isset($_POST['total_amount'])) {
    $customer_name = mysqli_real_escape_string($db, $_POST['customer_name']);
    $reference_no = mysqli_real_escape_string($db, $_POST['reference_no']);
    $total_amount = mysqli_real_escape_string($db, $_POST['total_amount']);
    $user_id = $_SESSION['user_id']; // Assuming you have stored the user ID in the session

    // Insert the order into the orders table
    $insert_order_query = "INSERT INTO orders (user_id, customer_name, reference_no, total_amount, order_date) 
                           VALUES ('$user_id', '$customer_name', '$reference_no', '$total_amount', NOW())";

    if (mysqli_query($db, $insert_order_query)) {
        $order_id = mysqli_insert_id($db); // Get the ID of the newly created order

        // Fetch cart items for order details
        $cart_query = "SELECT cart.ticket_id, tickets.ticket_name, tickets.price 
                       FROM cart 
                       JOIN tickets ON cart.ticket_id = tickets.id 
                       WHERE cart.user_id = '$user_id'";

        $cart_result = mysqli_query($db, $cart_query);

        while ($cart_item = mysqli_fetch_assoc($cart_result)) {
            $ticket_id = $cart_item['ticket_id'];
            $ticket_name = mysqli_real_escape_string($db, $cart_item['ticket_name']);
            $price = mysqli_real_escape_string($db, $cart_item['price']);

            // Insert each item into order_details
            $insert_detail_query = "INSERT INTO order_details (order_id, ticket_id, ticket_name, price) 
                                    VALUES ('$order_id', '$ticket_id', '$ticket_name', '$price')";
            mysqli_query($db, $insert_detail_query);
        }

        // Optionally, clear the cart after successful order
        $clear_cart_query = "DELETE FROM cart WHERE user_id = '$user_id'";
        mysqli_query($db, $clear_cart_query);

        echo "Order submitted successfully.";
        // Redirect to a success page or display a success message
        header('Location: order_success.php'); // Create this page to show order success
        exit();
    } else {
        echo "Error: " . mysqli_error($db);
    }
} else {
    echo "Invalid request.";
}
?>
