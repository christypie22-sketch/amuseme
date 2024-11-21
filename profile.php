<?php 
session_start();
include ("components/connect.php");

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the search query
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
}

// Function to check if a ticket is in favorites
function isFavorite($ticketId, $userId, $conn) {
    $query = "SELECT * FROM favorites WHERE user_id = '$userId' AND ticket_id = '$ticketId'";
    $result = $conn->query($query);
    return ($result && $result->num_rows > 0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AmuseMe</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Styles for heart icon */
        .favorite-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.5em;
            color: #999;
        }
        .favorite-btn.favorite {
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <br><br>
    <div class="search-bar">
        <form action="" method="GET">
            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search Ticket">
        </form>
    </div>

    <div class="row">
        <div class="col-md-9">
            <h3 style="font-weight: bold;"><img src="img/ticket_icon_2.png" alt="" style="width: 60px;"> Available Tickets</h3>
            <div class="row">
                <?php
                // Fetch tickets based on search query
                $sql = "SELECT * FROM tickets WHERE `type`='regular'";
                if (!empty($searchQuery)) {
                    $sql .= " AND ticket_name LIKE '%$searchQuery%'";
                }

                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $isFavorite = isset($_SESSION['user_id']) ? isFavorite($row['id'], $_SESSION['user_id'], $conn) : false;
                        $heartClass = $isFavorite ? 'favorite' : '';
                        echo '
                        <div class="col-md-4">
                            <div class="ticket">
                                <a href="ticket_detail.php?id='.$row['id'].'" style="text-decoration: none; color: inherit;">
                                    <img src="qr_image/'.$row['image'].'" alt="Ticket Image">
                                    <div class="ticket-name">'.$row['ticket_name'].'</div>
                                    <div class="ticket-price">P '.$row['price'].'</div>
                                    <div>
                                        <span class="star">&#9733;</span>
                                        <span class="cart">&#128722;</span>
                                    </div>
                                </a>
                                
                                <!-- Add to Cart Form -->
                                <form action="add_to_cart.php" method="POST">
                                    <input type="hidden" name="ticket_id" value="'.$row['id'].'">
                                    <input type="hidden" name="ticket_price" value="'.$row['price'].'">
                                    <button type="submit" class="btn btn-primary" onclick="return checkLogin();">Add to Cart</button>
                                </form>

                                <!-- Favorite Button -->
                                <button class="favorite-btn '.$heartClass.'" data-ticket-id="'.$row['id'].'" onclick="toggleFavorite(this)">
                                    &#9829;
                                </button>
                            </div>
                        </div>';
                    }
                }
                ?>    
            </div>
        </div>
    </div>
</div>

<script>
    function checkLogin() {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('Please sign in to add tickets to your cart.');
            window.location.href = 'signin.php';
            return false;
        <?php endif; ?>
        return true;
    }

    // Toggle favorite status
    function toggleFavorite(button) {
    const ticketId = button.getAttribute('data-ticket-id');
    const isFavorite = button.classList.contains('favorite');
    
    fetch('toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ticket_id: ticketId, action: isFavorite ? 'remove' : 'add' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.toggle('favorite');
        } else {
            alert(data.message || 'An error occurred. Please try again.');
            if (data.message === 'Please log in to manage favorites.') {
                window.location.href = 'signin.php';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php $conn->close(); ?>
