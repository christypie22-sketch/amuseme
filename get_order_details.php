<?php
include('components/connect.php');

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];

    // Fetch order details along with ticket images
    $order_query = "SELECT o.id, o.customer_name, o.reference_no, o.total_amount, o.order_date, o.status, o.is_scanned AS is_scanned, o.is_feedback_done AS is_feedback_done, oi.ticket_name, oi.price, t.image, t.id AS ticket_id
                    FROM orders o
                    JOIN order_details oi ON o.id = oi.order_id 
                    JOIN tickets t ON oi.ticket_id = t.id 
                    WHERE o.id = '$orderId'";
    
    $result = mysqli_query($db, $order_query);
    
    $orderDetails = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orderDetails['id'] = $row['id'];
            $orderDetails['customer_name'] = $row['customer_name'];
            $orderDetails['reference_no'] = $row['reference_no'];
            $orderDetails['total_amount'] = $row['total_amount'];
            $orderDetails['order_date'] = $row['order_date'];
            $orderDetails['status'] = $row['status'];
            $orderDetails['ticket_id'] = $row['ticket_id'];
            $orderDetails['is_scanned'] = (int) $row['is_scanned'];
            $orderDetails['is_feedback_done'] = (int) $row['is_feedback_done'];
            $orderDetails['items'][] = [
                'ticket_name' => $row['ticket_name'],
                'price' => $row['price'],
                'image' => $row['image'], // Include image field
            ];
        }
    }

    // Return the order details as JSON
    header('Content-Type: application/json');
    echo json_encode($orderDetails);
}
?>
