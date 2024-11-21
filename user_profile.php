<?php 
include('components/connect.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define the getTodayOrders function
function getTodayOrders() {
    global $db;

    date_default_timezone_set('Asia/Manila'); // Adjust to your timezone

    // Get today's date in the format used by the `order_date` column
    $today = date('Y-m-d');

    // Query to select orders where the order_date is today's date
    // Use the date range for the whole day
    $query = "SELECT * FROM orders WHERE order_date >= '$today 00:00:00' AND order_date < DATE_ADD('$today', INTERVAL 1 DAY)";
    $result = mysqli_query($db, $query);

    // Initialize an array to store today's orders
    $todayOrders = [];
    
    // Fetch the orders and add them to the array
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $todayOrders[] = $row;
        }
    }

    return $todayOrders;
}


$avatar_path = 'avatar/user-icon.png'; 

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    if (isset($_GET['feedbackMessage'])) {
        $feedbackMessage = htmlspecialchars($_GET['feedbackMessage'], ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$feedbackMessage');</script>";
    }

    // Check if avatar file is uploaded
    if (isset($_FILES['avatar'])) {
        // File info
        $avatar = $_FILES['avatar'];
        $avatar_name = $avatar['name'];
        $avatar_tmp_name = $avatar['tmp_name'];
        $avatar_error = $avatar['error'];
        $avatar_size = $avatar['size'];

        // Check for upload error
        if ($avatar_error === 0) {
            // Set a 2MB size limit
            if ($avatar_size <= 2000000) {
                // Allowed file extensions
                $allowed_ext = ['jpg', 'jpeg', 'png'];
                $avatar_ext = strtolower(pathinfo($avatar_name, PATHINFO_EXTENSION));

                if (in_array($avatar_ext, $allowed_ext)) {
                    // Create unique filename
                    $new_avatar_name = uniqid('', true) . "." . $avatar_ext;
                    $avatar_upload_path = 'avatar/' . $new_avatar_name;

                    // Move file to avatar directory
                    if (move_uploaded_file($avatar_tmp_name, $avatar_upload_path)) {
                        // Fetch user ID from session
                        $query = "SELECT id FROM users WHERE email='$email' LIMIT 1";
                        $result = mysqli_query($db, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            $user_data = mysqli_fetch_assoc($result);
                            $user_id = $user_data['id'];

                            // Update avatar in database
                            $update_avatar_query = "UPDATE users SET avatar='$new_avatar_name' WHERE id='$user_id'";
                            if (mysqli_query($db, $update_avatar_query)) {
                                echo "<script>alert('Avatar updated successfully!');</script>";
                            } else {
                                echo "Error updating avatar: " . mysqli_error($db);
                            }
                        }
                    } else {
                        echo "<script>alert('Failed to upload avatar.');</script>";
                    }
                } else {
                    echo "<script>alert('Invalid file type. Only JPG, JPEG, and PNG files are allowed.');</script>";
                }
            } else {
                echo "<script>alert('File size exceeds 2MB.');</script>";
            }
        } else {
            echo "<script>alert('Error uploading file.');</script>";
        }
    }

    // Fetch user avatar from database
    $query = "SELECT avatar FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($db, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if ($user['avatar']) {
            $avatar_path = 'avatar/' . $user['avatar'];
        }
    }
}

//UPDATE PROFILE
if (isset($_POST['update_profile'])) {
    // Get form data
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $contact_no = mysqli_real_escape_string($db, $_POST['contact_no']);
    $session_email = $_SESSION['email']; // Current logged-in user's email

    // Update query
    $query = "UPDATE users SET username='$username', email='$email', contact_no='$contact_no' WHERE email='$session_email'";
    
    if (mysqli_query($db, $query)) {
        // Update session if the email changes
        $_SESSION['email'] = $email;
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . mysqli_error($db);
    }
}

// Fetch the updated user data
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Fetch the user's details from the database
    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $name = $user['username'];
        $email = $user['email'];
        $contact_no = $user['contact_no'];
    } else {
        $error_message = "Failed to retrieve user data.";
    }
} else {
    header('location: signin.php');
    exit();
}

//DELETE PROFILE
if (isset($_POST['delete_account'])) {
    $session_email = $_SESSION['email']; // Current logged-in user's email

    // Delete query
    $delete_query = "DELETE FROM users WHERE email='$session_email'";

    if (mysqli_query($db, $delete_query)) {
        // Destroy session and redirect to sign-in page
        session_destroy();
        header('location: signin.php');
        exit();
    } else {
        $error_message = "Error deleting account: " . mysqli_error($db);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Amuse Me</title>
    <link rel="icon" href="./img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="style/fontawesome.css">
    <script src="./js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./style/Style.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css"> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        
        .cart-items {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px 0;
        }

        .cart-item {
            display: flex;
            align-items: center;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 100%;
            max-width: 400px;
        }

        .cart-image {
            margin-right: 15px;
        }

        .qr-code {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .cart-info {
            flex: 1;
        }

        .ticket-name {
            font-size: 18px;
            font-weight: bold;
        }

        .ticket-price {
            font-size: 16px;
            color: #555;
        }
    </style>
</head>
<body>

    <!-- HEADER START -->
        <?php include 'components/user_header.php'; ?>
    <!-- HEADER END -->

    <section class="user-container">
        <div class="col" style="width: 100%;">
        <div class="profile-cont">
    <h3>Personal Information</h3>
    <div class="profile-info">
        <div>
            <img src="<?php echo htmlspecialchars($avatar_path); ?>" alt="User Avatar" id="user-avatar" onerror="this.src='avatar/user-icon.png';">
            <br>
            <form method="POST" action="user_profile.php" enctype="multipart/form-data" id="avatar-form">
                <input type="file" name="avatar" accept="image/*" id="avatar-input" style="display:none;" onchange="document.getElementById('avatar-form').submit();">
                <button type="button" onclick="document.getElementById('avatar-input').click();" style="background-color: #04AA6D;">Change Avatar</button>
            </form>
        </div>
        <?php
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];

            // Fetch the user's details from the database
            $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
            $result = mysqli_query($db, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                $name = $user['username'];
                $email = $user['email'];
                $contact_no = $user['contact_no'];
        ?>
        <form method="POST" action="" class="profile-label">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="input-group">
                <label for="contact_no">Contact No.</label>
                <input type="number" name="contact_no" id="contact_no" value="<?php echo htmlspecialchars($contact_no); ?>">
            </div>
            <div class="button-group">
                <button type="submit" name="update_profile">Update Info</button>
                <button type="submit" name="delete_account" style="background-color: #f44336;">Delete Account</button>
            </div>
        </form>
        <?php
            } else {
                echo "<p>Failed to retrieve user data</p>";
            }
        } else {
            header('location: signin.php');
            exit();
        }
        ?>
    </div>
</div>

            
    <!-- FAVORITE ADD TICKET SECTION -->
    <div class="fav-add">
        <p class="fav-add-nav">
            
            <a class="active" href="#" id="addcart">Tickets in Cart</a>
            <a href="#" id="favorites">Favorites</a>
            <a href="#" id="purchased">Purchased History</a>
        </p>
        <div class="grid-box">
            <!-- Display Cart Items Here -->
            <?php
            $total_amount = 0;
            $cart_total_amount = 0;
            if (isset($_SESSION['email'])) {
                $email = $_SESSION['email'];
                
                // Fetch the user ID
                $user_query = "SELECT id FROM users WHERE email='$email' LIMIT 1";
                $user_result = mysqli_query($db, $user_query);
                if ($user_result && mysqli_num_rows($user_result) > 0) {
                    $user_data = mysqli_fetch_assoc($user_result);
                    $user_id = $user_data['id'];

                    // Fetch the cart items
                    $cart_query = "SELECT cart.ticket_id, tickets.ticket_name, tickets.price, tickets.ride_image 
                                FROM cart 
                                JOIN tickets ON cart.ticket_id = tickets.id 
                                WHERE cart.user_id = '$user_id'";
                    $cart_result = mysqli_query($db, $cart_query);

                    if ($cart_result && mysqli_num_rows($cart_result) > 0) {
                        while ($cart_item = mysqli_fetch_assoc($cart_result)) {
                            echo '<div class="box addcart-box" style="width: 350px;">';
                            echo '<div class="box-image" style="height: 170px;">';
                            echo '<center><br><img src="ride_image/' . htmlspecialchars($cart_item['ride_image']) . '" alt="QR Code" style="width: 150px;">';
                            echo '</div>';
                            echo '<div class="box-info">';
                            echo '<p style="text-align: center;">' . htmlspecialchars($cart_item['ticket_name']) . '</p>';
                            echo '<p style="text-align: center;">Price: P ' . htmlspecialchars($cart_item['price']) . '</p></center>';
                            $cart_total_amount += $cart_item['price']; // Calculate total amount
                            
                            // Delete button
                            echo '<form method="POST" action="delete_ticket.php" style="display:inline;">';
                            echo '<input type="hidden" name="ticket_name" value="' . htmlspecialchars($cart_item['ticket_name']) . '">';
                            echo '<input type="hidden" name="ticket_id" value="' . htmlspecialchars($cart_item['ticket_id']) . '">';
                            echo '<br><button type="submit" name="delete_ticket" style="color:red; background:none; border:none; cursor:pointer;">';
                            echo '<i class="fas fa-trash-alt" style="font-size: 20px;"></i> Delete</button>';
                            echo '</form>';
                            
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No tickets in cart.</p>';
                    }
                } else {
                    echo '<p>User not found.</p>';
                }
            } else {
                echo '<p>Please log in to see your cart.</p>';
            }
            ?>
        </div>
    
    <!-- FAVORITES SECTION -->
    <div class="grid-box" >
        <!-- Display Cart Items Here -->
        <?php
        
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
            
            // Fetch the user ID
            $user_query = "SELECT id FROM users WHERE email='$email' LIMIT 1";
            $user_result = mysqli_query($db, $user_query);
            if ($user_result && mysqli_num_rows($user_result) > 0) {
                $user_data = mysqli_fetch_assoc($user_result);
                $user_id = $user_data['id'];

                // Fetch the favorites items
                $favorites_query = "SELECT favorites.ticket_id, tickets.ticket_name, tickets.price, tickets.ride_image 
                               FROM favorites 
                               JOIN tickets ON favorites.ticket_id = tickets.id 
                               WHERE favorites.user_id = '$user_id'";
                $favorites_result = mysqli_query($db, $favorites_query);

                if ($favorites_result && mysqli_num_rows($favorites_result) > 0) {
                    while ($favorites_item = mysqli_fetch_assoc($favorites_result)) {
                        echo '<div class="box favorite-box" style="width: 350px; display: none;">';
                        echo '<div class="box-image" style="height: 170px;">';
                        echo '<center><br><img src="ride_image/' . htmlspecialchars($favorites_item['ride_image']) . '" alt="QR Code" style="width: 150px;">';
                        echo '</div>';
                        echo '<div class="box-info">';
                        echo '<p style="text-align: center;">' . htmlspecialchars($favorites_item['ticket_name']) . '</p>';
                        echo '<p style="text-align: center;">Price: P ' . htmlspecialchars($favorites_item['price']) . '</p></center>';
                        
                        
                        // Delete button
                        echo '<form method="POST" action="delete_ticket.php" style="display:inline;">';
                        echo '<input type="hidden" name="ticket_name" value="' . htmlspecialchars($favorites_item['ticket_name']) . '">';
                        echo '<input type="hidden" name="ticket_id" value="' . htmlspecialchars($favorites_item['ticket_id']) . '">';
                        echo '<br><button type="submit" name="delete_ticket" style="color:red; background:none; border:none; cursor:pointer;">';
                        echo '<i class="fas fa-trash-alt" style="font-size: 20px;"></i> Delete</button>';
                        echo '</form>';

                        echo '<form method="POST" action="add_to_cart_from_fav.php" style="display:inline;">';
                        echo '<input type="hidden" name="ticket_id" value="' . htmlspecialchars($favorites_item['ticket_id']) . '">';
                        echo '<input type="hidden" name="price" value="' . htmlspecialchars($favorites_item['price']) . '">';
                        echo '<br><button type="submit" name="delete_ticket" style="color:green; background:none; border:none; cursor:pointer;">';
                        echo '<i class="fa-solid fa-cart-plus style="font-size: 20px;"></i> Add to Cart</button>';
                        echo '</form>';
                        
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No tickets in favorites.</p>';
                }
            } else {
                echo '<p>User not found.</p>';
            }
        } else {
            echo '<p>Please log in to see your favorites.</p>';
        }
        ?>
    </div>

    <!-- PURCHASE HISTORY SECTION -->
    <div id="purchase-history" class="mt-4" style="display: none;">
        <h3>Purchase History</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Reference No</th>
                        <th>Total Amount</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Initialize a counter variable
                $counter = 1;

                // PHP code to fetch and display purchase history
                $purchase_query = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC";
                $purchase_result = mysqli_query($db, $purchase_query);
                
                if ($purchase_result && mysqli_num_rows($purchase_result) > 0) {
                    while ($order = mysqli_fetch_assoc($purchase_result)) {
                        echo '<tr>';
                        echo '<td>' . $counter . '</td>'; // Sequential numbering
                        echo '<td>' . htmlspecialchars($order['customer_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($order['reference_no']) . '</td>';
                        echo '<td>P ' . htmlspecialchars($order['total_amount']) . '</td>';
                        echo '<td>' . htmlspecialchars($order['order_date']) . '</td>';
                        echo '<td><button class="btn btn-info view-order" data-id="' . htmlspecialchars($order['id']) . '" data-toggle="modal" data-target="#orderDetailsModal">View</button></td>';
                        echo '</tr>';

                        // Increment the counter
                        $counter++;
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center">No purchase history found.</td></tr>';
                }
                ?>
            </tbody>


            </table>
        </div>
    </div>

    <hr>
    <div class="checkout">
        <h4>Total Amount: P <?php echo number_format($cart_total_amount, 2); ?></h4>
        <button class="btn btn-primary" data-toggle="modal" data-target="#orderModal">Checkout</button>
    </div>
</div>


    <!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close" id="modalCloseButton" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="order-details-content">
                    <!-- Order details will be populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="modalCloseButtonFooter">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.9);">
    <div class="modal-dialog modal-lg" style="margin-top:65px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">Feedback</h5>
                <button type="button" class="btn-close" id="feedbackModalCloseButton" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="feedbackForm" action="submit_feedback_ride.php" method="POST">
                    <div class="form-group">
                        <label for="feedbackText">Your Feedback:</label>
                        <textarea class="form-control" id="feedbackText" name="feedbackText" rows="4" placeholder="Enter your feedback here" required></textarea>
                    </div>
                    <div class= "form-group mt-3">
                        <label for="rating">Rating:</label>
                        <select class="form-control" id="rating" name="rating" required>
                            <option value="" disabled selected>Choose a rating</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Fair</option>
                            <option value="3">3 - Good</option>
                            <option value="4">4 - Very Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                        <input type="hidden" name="order_id" id="order_id">
                        <input type="hidden" name="current_ticket_id" id="current_ticket_id">
                    </div>
                    <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="feedbackModalCloseButtonFooter">Close</button>
                <button type="submit" form="feedbackForm" class="btn btn-primary">Submit Feedback</button>
            </div>
                </form>
            </div>
            
        </div>
    </div>
</div>



<!-- Order Summary Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <?php 
                    $todayOrders = getTodayOrders();
                    $disableSubmitButton = count($todayOrders) >= 16; 
                    
                ?>
                <h5 class="modal-title" id="orderModalLabel">Order Summary</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <center>
                <p>GCash Number: <strong>0921-123-4567</strong></p>
                <p style="font-size: 18px; font-weight: bold;">SCAN TO PAY:</p>
                <img src="payment/gcash_qr.JPEG" alt="GCash QR Code" class="qr-code" style="width: 250px; height: auto;">
                <p style="font-size: 15px; font-weight: bold; margin-top: 10px;">TOTAL AMOUNT TO PAY: P<?php echo $cart_total_amount; ?></p></center><br><br>
                <?php if ($disableSubmitButton): ?>
    <div style="color: red; font-weight: bold; margin-bottom: 10px;">
        The daily order limit of 20 has been reached. New orders cannot be submitted today.
    </div>
<?php endif; ?>

<form id="orderForm" method="POST" action="submit_order.php">
    <input type="hidden" name="total_amount" value="<?php echo $cart_total_amount; ?>">
    <div class="form-group">
        <label for="customer_name">Customer Name:</label>
        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
    </div>
    <div class="form-group">
        <label for="reference_no">Reference No:</label>
        <input type="text" class="form-control" id="reference_no" name="reference_no" required>
    </div>
    <button type="submit" class="btn btn-success" 
        <?php echo $disableSubmitButton ? 'disabled' : ''; ?>>
        Submit Order
    </button>
</form>

            </div>
        </div>
    </div>
</div>

            </div>
        </div>
    </section>

<!-- Footer -->
  <?php include 'components/footer.php'; ?>
<!-- Footer -->

<script>
    $(document).ready(function() {
        // Function to open the modal and load order details
        function openOrderDetailsModal(orderId) {
            // Load order details via AJAX or populate it here
            $("#order-details-content").html("Loading..."); // Example loading message
            
            // Simulating order details loading
            setTimeout(function() {
                $("#order-details-content").html("<p>Order ID: " + orderId + "</p><p>Details: Example details go here.</p>");
            }, 1000);

            // Show the modal
            $("#orderDetailsModal").modal('show');
        }

        $(document).on("click", ".btn[data-target='#feedbackModal']", function() {
            // Get the order ID from the data-id attribute
            const orderId = $(this).data("id");
            const ticketId = $(this).data("ticket")

             // Log to console for debugging
            console.log("Order ID: " + orderId);
            console.log("Ticket ID: " + ticketId);
            
            // Set the order_id input in the feedback form
            $("#order_id").val(orderId);
            $("#current_ticket_id").val(ticketId);
        });

        // Event listener for view button click
        $(".view-button").on("click", function() {
            const orderId = $(this).data("order-id"); // Get the order ID from the button's data attribute
            openOrderDetailsModal(orderId);
        });

        // Event listeners for closing the modal
        $("#modalCloseButton, #modalCloseButtonFooter").on("click", function() {
            $("#orderDetailsModal").modal('hide'); // Close the modal
        });
    });
</script>


    <!-- Script -->
    <script>
        // Script for toggling the purchase history and cart items
        document.addEventListener('DOMContentLoaded', function() {
        // On load, show only the cart items
        document.querySelectorAll('.addcart-box').forEach(function(box) {
            box.style.display = 'block'; // Show cart items
        });
        document.querySelectorAll('.favorite-box').forEach(function(box) {
            box.style.display = 'none'; // Hide favorites items
        });
        document.getElementById('purchase-history').style.display = 'none'; // Hide purchase history
        
        // Set initial active tab for 'Tickets in Cart'
        document.getElementById('addcart').classList.add('active');
        document.getElementById('favorites').classList.remove('active');
        document.getElementById('purchased').classList.remove('active');
    });

        // Show Cart Items
        document.getElementById('addcart').addEventListener('click', function(event) {
            event.preventDefault();
            document.querySelectorAll('.addcart-box').forEach(function(box) {
                box.style.display = 'block'; // Show cart items
            });
            document.querySelectorAll('.favorite-box').forEach(function(box) {
                box.style.display = 'none'; // Hide favorites
            });
            document.getElementById('purchase-history').style.display = 'none'; // Hide purchase history
            
            document.getElementById('addcart').classList.add('active');
            document.getElementById('favorites').classList.remove('active');
            document.getElementById('purchased').classList.remove('active');
        });

        // Show Favorites Items
        document.getElementById('favorites').addEventListener('click', function(event) {
            event.preventDefault();
            document.querySelectorAll('.addcart-box').forEach(function(box) {
                box.style.display = 'none'; // Hide cart items
            });
            document.querySelectorAll('.favorite-box').forEach(function(box) {
                box.style.display = 'block'; // Show favorites
            });
            document.getElementById('purchase-history').style.display = 'none'; // Hide purchase history
            
            document.getElementById('favorites').classList.add('active');
            document.getElementById('addcart').classList.remove('active');
            document.getElementById('purchased').classList.remove('active');
        });

        // Show Purchase History
        document.getElementById('purchased').addEventListener('click', function(event) {
            event.preventDefault();
            document.querySelectorAll('.addcart-box').forEach(function(box) {
                box.style.display = 'none'; // Hide cart items
            });
            document.querySelectorAll('.favorite-box').forEach(function(box) {
                box.style.display = 'none'; // Hide favorites
            });
            document.getElementById('purchase-history').style.display = 'block'; // Show purchase history
            
            document.getElementById('purchased').classList.add('active');
            document.getElementById('favorites').classList.remove('active');
            document.getElementById('addcart').classList.remove('active');
        });


// Script to handle view order button click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('view-order')) {
        const orderId = e.target.getAttribute('data-id');
        
        // Fetch order details from the server using AJAX
        fetch(`get_order_details.php?id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                    const orderDetailsContent = `
                    <h5>Order ID: ${data.id}</h5>
                    <p><strong>Customer Name:</strong> ${data.customer_name}</p>
                    <p><strong>Reference No:</strong> ${data.reference_no}</p>
                    <p><strong>Total Amount:</strong> P ${data.total_amount}</p>
                    <p><strong>Date:</strong> ${data.order_date}</p>
                    <p><strong>Status:</strong><span style="color: orange;"> ${data.status}</span></p>
                    ${data.status === "completed" ? `
                <h5>Order Items:</h5>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    ${data.items.map(item => `
                        <div style="flex: 0 0 150px; text-align: center;">
                            <h6>${item.ticket_name}</h6>
                            <p>P ${item.price}</p>
                            <img src="qr_image/${item.image}" alt="${item.ticket_name}" style="width: 100px; height: auto; border-radius: 5px;">
                        </div>
                    `).join('')}
                </div>
            ` : ""}
                    <p>
                        ${data.is_scanned === 1 && data.is_feedback_done === 0
                            ? `<button class="btn btn-info" data-id="${data.id}" data-ticket="${data.ticket_id}" data-toggle="modal" data-target="#feedbackModal">Feedback</button>`
                            : ``
                        }
                    </p>
        `;
                document.getElementById('order-details-content').innerHTML = orderDetailsContent;
            })
            .catch(error => console.error('Error fetching order details:', error));
    }

   
});

    </script>
