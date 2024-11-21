<?php 
session_start(); // Start the session
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AmuseMe</title>
    <link rel="stylesheet" href="style/fontawesome.css">
    <script src="./js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./style/Style.css">
    <style>
        /* Toggler button placed at the right */
        .toggler-btn {
            position: fixed;
            bottom: 50px;
            right: 20px; /* Positioned on the right */
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            z-index: 1000;
        }

        /* Background overlay with 50% opacity black */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* 50% opacity black background */
            display: none;
            z-index: 999; /* Behind the image popup */
        }

        /* Image container centered, 90% width and height */
        .image-popup {
            position: fixed;
            top: 50%; 
            left: 50%;
            transform: translate(-50%, -50%); /* Center the popup */
            width: 69%;
            height: 90%;
            background-color: rgba(0, 0, 0, 0.5); /* 50% opacity black background */
            /* background-color: transparent; */
            border-radius: 10px;
            display: none; /* Initially hidden */
            z-index: 1000;
            overflow: hidden;
        }

        .image-popup img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Ensures the entire image is visible without cropping */
            border-radius: 10px;
            transition: transform 0.3s ease; /* Smooth transition for zoom */
            cursor: grab; /* Show a grabbing cursor */
        }

        /* Zoom controls */
        .zoom-controls {
            position: absolute;
            bottom: 20px;
            right: 20px; /* Positioned in the bottom right corner */
            display: flex;
            flex-direction: column; /* Stack buttons vertically */
            gap: 10px;
        }

        .zoom-controls button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Close button */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <!-- HEADER START -->
        <?php include 'components/user_header.php'; ?>
    <!-- HEADER END -->

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

                // Modify query if there's a search term
                if (!empty($searchQuery)) {
                    $sql .= " AND ticket_name LIKE '%$searchQuery%'";
                }

                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '
                        <div class="col-md-4">
                            <div class="ticket">
                                <a href="ticket_detail.php?id='.$row['id'].'" style="text-decoration: none; color: inherit;">
                                    <img src="img/ticket_icon.png" alt="" style="width: 30px; height: 30px; float: left; margin-right: 70px;">
                                    <img src="ride_image/'.$row['ride_image'].'" alt="Ticket Image">
                                    <div class="ticket-name">'.$row['ticket_name'].'</div>
                                    <div class="ticket-price">P '.$row['price'].'</div>
                                    <div>
                                        <span class="star">&#9733;</span>
                                        <span class="heart">&#9829;</span>
                                        <span class="cart">&#128722;</span>
                                    </div>
                                </a>
                                <!-- Add to Cart Form -->
                                <form action="add_to_cart.php" method="POST">
                                    <input type="hidden" name="ticket_id" value="'.$row['id'].'">
                                    <input type="hidden" name="ticket_price" value="'.$row['price'].'">
                                    <button type="submit" class="btn btn-primary" onclick="return checkLogin();">Add to Cart</button>
                                </form>
                                <!-- Add to Favorites Form -->
                                <form action="add_to_favorites.php" method="POST" style="margin-top: 10px;">
                                    <input type="hidden" name="ticket_id" value="'.$row['id'].'">
                                    <button type="submit" class="btn btn-secondary" onclick="return checkLogin();">Add to Favorites</button>
                                </form>
                            </div>
                        </div>';
                    }
                }
                ?>    
            </div>
        </div>

        <div class="col-md-3" style="background-color: #404040; padding: 20px; border-radius: 10px;">
            <h3 style="font-weight: bold; color: white;">Promo Tickets</h3>
            <?php
            // Fetch promo tickets from the database (Assume 'promo_tickets' table)
            $promo_sql = "SELECT * FROM tickets WHERE type='promo'";
            if (!empty($searchQuery)) {
                $promo_sql .= " AND ticket_name LIKE '%$searchQuery%'";
            }
            $promo_result = $conn->query($promo_sql);
            if ($promo_result->num_rows > 0) {
                while ($promo = $promo_result->fetch_assoc()) {
                    echo '
                    <div class="promo-ticket">
                     <a href="ticket_detail.php?id='.$promo['id'].'" style="text-decoration: none; color: inherit;">
                        <img src="ride_image/'.$promo['ride_image'].'" alt="Promo Image">
                        <div class="promo-title">'.$promo['ticket_name'].'</div>
                        <div class="promo-price">P '.$promo['price'].'</div>
                        <div>
                            <span class="star">&#9733;</span>
                            <span class="heart">&#9829;</span>
                            <span class="cart">&#128722;</span>
                            <!-- Add to Cart Form -->
                                <form action="add_to_cart.php" method="POST">
                                    <input type="hidden" name="ticket_id" value="'.$promo['id'].'">
                                    <input type="hidden" name="ticket_price" value="'.$promo['price'].'">
                                    <button type="submit" class="btn btn-primary" onclick="return checkLogin();">Add to Cart</button>
                                </form>
                            <!-- Add to Favorites Form -->
                                <form action="add_to_favorites.php" method="POST" style="margin-top: 10px;">
                                    <input type="hidden" name="ticket_id" value="'.$promo['id'].'">
                                    <button type="submit" class="btn btn-secondary" onclick="return checkLogin();">Add to Favorites</button>
                                </form>
                        </div>
                        </a>
                    </div>';
                }
            } else {
                echo "<p style='color: white;'>No promo tickets found</p>";
            }
            ?>
        </div>
    </div>
</div>
<!-- Background Overlay -->
<div class="overlay" id="overlay" onclick="toggleImage()"></div>

<!-- Toggler Button for Image Popup -->
<button class="toggler-btn" onclick="toggleImage()">
<i class="fas fa-map"></i>
</button>
<!-- Image Popup Container -->
<div class="image-popup" id="imagePopup">
    <!-- Close Button inside the popup -->
    <button class="close-btn" onclick="toggleImage()">×</button>
    <img src="./img/map.jpg" alt="Map Image" id="popupImage">
    
    <!-- Zoom controls positioned in the bottom right corner -->
    <div class="zoom-controls">
        <button onclick="zoomIn()">Zoom In</button>
        <button onclick="zoomOut()">Zoom Out</button>
    </div>
</div>

<script>
    function checkLogin() {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('Please sign in to add tickets to your cart.');
            window.location.href = 'signin.php'; // Redirect to sign-in page
            return false; // Prevent form submission
        <?php endif; ?>
        return true; // Allow form submission
    }

    let scale = 1;
        let translateX = 0;
        let translateY = 0;
        let isDragging = false;
        let startX, startY;

        // Toggle Image Popup
        function toggleImage() {
            const imagePopup = document.getElementById("imagePopup");
            const overlay = document.getElementById("overlay");

            if (imagePopup.style.display === "none" || imagePopup.style.display === "") {
                imagePopup.style.display = "block";
                overlay.style.display = "block"; // Show the background overlay
            } else {
                imagePopup.style.display = "none";
                overlay.style.display = "none"; // Hide the background overlay
                resetZoomAndPan(); // Reset zoom and pan when the popup is closed
            }
        }

        // Zoom In function
        function zoomIn() {
            scale += 0.1; // Increase scale
            updateImageTransform(); // Apply zoom and pan
        }

        // Zoom Out function
        function zoomOut() {
            if (scale > 0.1) {
                scale -= 0.1; // Decrease scale
                updateImageTransform(); // Apply zoom and pan
            }
        }

        // Reset zoom and pan
        function resetZoomAndPan() {
            scale = 1;
            translateX = 0;
            translateY = 0;
            updateImageTransform();
        }

        // Update image transform based on zoom and pan values
        function updateImageTransform() {
            const image = document.getElementById("popupImage");
            image.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
        }

        // Handle dragging for panning
        const image = document.getElementById("popupImage");
        image.addEventListener("mousedown", (e) => {
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            image.style.cursor = "grabbing";
        });

        image.addEventListener("mousemove", (e) => {
            if (isDragging) {
                translateX = e.clientX - startX;
                translateY = e.clientY - startY;
                updateImageTransform(); // Apply the pan movement
            }
        });

        image.addEventListener("mouseup", () => {
            isDragging = false;
            image.style.cursor = "grab";
        });

        image.addEventListener("mouseleave", () => {
            isDragging = false;
            image.style.cursor = "grab";
        });

        // For touch devices (mobile support)
        image.addEventListener("touchstart", (e) => {
            const touch = e.touches[0];
            isDragging = true;
            startX = touch.clientX - translateX;
            startY = touch.clientY - translateY;
        });

        image.addEventListener("touchmove", (e) => {
            if (isDragging) {
                const touch = e.touches[0];
                translateX = touch.clientX - startX;
                translateY = touch.clientY - startY;
                updateImageTransform();
            }
        });

        image.addEventListener("touchend", () => {
            isDragging = false;
        });


</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php
$conn->close();
?>

<?php 
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
?>