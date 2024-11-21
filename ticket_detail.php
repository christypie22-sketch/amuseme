<?php 
session_start(); // Start the session
include ("components/connect.php"); // Include your database connection

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get ticket ID from URL
if (isset($_GET['id'])) {
    $ticket_id = intval($_GET['id']); // Ensure it's an integer
} else {
    die("No ticket specified.");
}

// Fetch ticket details
$sql = "SELECT * FROM tickets WHERE id = $ticket_id";
$ticket_result = $conn->query($sql);
$ticket = $ticket_result->fetch_assoc();

// Fetch feedback with user information for this ticket
$feedback_sql = "
    SELECT feedback_ride.feedback, feedback_ride.created_at, feedback_ride.rating, users.username, users.avatar
    FROM feedback_ride
    JOIN users ON feedback_ride.user_id = users.id
    WHERE feedback_ride.ticket_id = $ticket_id;
";
$feedback_result = $conn->query($feedback_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($ticket['ticket_name']); ?> - Ticket Details</title>
    <link rel="stylesheet" href="style/fontawesome.css">
    <script src="./js/fontawesome.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./style/Style.css">
</head>
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

<body>
    <!-- HEADER START -->
        <?php include 'components/user_header.php'; ?>
    <!-- HEADER END -->


<div class="container">
    <div class="content-container" style="display: flex; flex-direction: column;">
            <!-- Ticket Container -->
        <div >
            <div style="display: flex; align-items: center;">
                <img src="ride_image/<?php echo $ticket['ride_image']; ?>" alt="Ticket Image" class="ticket-image">
                <div style="margin-top: 35px;">
                    <h1><?php echo htmlspecialchars($ticket['ticket_name']); ?></h1>
                    <div class="ticket-description">
                        <p style="text-align: justify"><b>Description:</b><br><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
                    </div>
                </div>
                
            </div>
            
            
        </div>

        <!-- Ticket Reviews in Ticket Item -->
        <div class="reviews_container">
            <h3 style="font-weight: bold;">
                Item Reviews
            </h3>

            <!-- Display reviews list -->
            <?php
            if ($feedback_result->num_rows > 0) {
                while ($feedback_row = $feedback_result->fetch_assoc()) {
                    // User profile image or a placeholder
                    $user_image = $feedback_row['avatar'] ? 'avatar/' . htmlspecialchars($feedback_row['avatar']) : 'avatar/user-icon.png';
                    
                    echo '<div class="reviews">
                        <img src="' . $user_image . '" alt="User Image">
                        <div class="user-info">
                            <span>' . htmlspecialchars($feedback_row['username']) . '</span>
                            <div class="ratings">';
                    
                    // Display stars based on the rating
                    for ($i = 0; $i < 5; $i++) {
                        if ($i < $feedback_row['rating']) {
                            echo '<span class="star" style="color: gold;">&#9733;</span>';
                        } else {
                            echo '<span class="star">&#9734;</span>'; // Empty star
                        }
                    }
                    
                    echo '</div>
                            <div class="reviews-text"><p style="text-align: justify">' . htmlspecialchars($feedback_row['feedback']) . '</p></div>
                            <div class="created-at"><p>posted at: ' . htmlspecialchars($feedback_row['created_at']) . '</p></div>
                        </div>
                    </div>';
                }
            } else {
                echo '<li class="list-group-item">No feedback available.</li>';
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
