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

// Fetch slides for the slideshow
$slides = $conn->query("SELECT * FROM slideshows");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Home | Amuse Me</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/fontawesome.css">
    <script src="./js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./style/Style.css">
    <style>
        .image-slider {
            padding-top: 54px;
            display: block;
            position: relative;
            overflow: hidden;
            width: 90vw;
            z-index: 1;
            margin: 10px auto;
        }

        .slider {
            width: 100%;
            height: 60vh;
            overflow: hidden;
            position: relative;
        }

        .img-container {
            display: flex;
            height: 100%;
            transition: 0.4s ease-in-out;
        }

        .img-container .img {
            flex-shrink: 0;
            height: 100%;
            width: 100vw;
            background-size: cover;
            position: relative;
        }

        .img-container .img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            background-position: center;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }

        .pagination span {
            background: #ebebeb;
            box-shadow: inset -5px -5px 10px 0px rgba(255, 255, 255, 0.5), inset 5px 5px 10px 0px rgba(0, 0, 0, 0.3);
            width: 0.5px;
            height: 0.5px;
            border-radius: 50%;
            cursor: pointer;
        }

        .pagination .active {
            box-shadow: none;
            background: #16558F;
        }

        .top-ticket {
            width: 90vw;
            margin: 10px auto;
        }

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

    <!-- slideshow start here -->
    <section class="image-slider">
        <div class="slider">
            <div class="img-container">
                <?php while ($row = $slides->fetch_assoc()) { ?>
                    <div class="img">
                    <img src="admin/<?= $row['image'] ?>" alt="<?= $row['caption'] ?>">
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="pagination">
            <?php for ($i = 0; $i < $slides->num_rows; $i++) { ?>
                <span <?= $i == 0 ? 'class="active"' : '' ?>></span>
            <?php } ?>
        </div>
    </section>
    <!-- slideshow end here -->

    <section class="top-ticket">
        <div>
            <h1>Top Selling Ticket</h1>
        </div>
        <div class="row">
        <div class="col-md-9">
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
    </section>

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

    <!-- Footer START -->
    <?php include 'components/footer.php'; ?>
    <!-- Footer END -->

<script>
	const slider = document.querySelector('.slider');
	const imgContainer = document.querySelector('.img-container');
	const pagination = document.querySelectorAll('.pagination span');

	// Set the width of the container based on the number of images and the viewport width
	const updateSlideWidth = () => {
		const viewportWidth = window.innerWidth;
		imgContainer.style.width = viewportWidth * pagination.length + 'px'; // Total width of all slides combined
		document.querySelectorAll('.img').forEach(img => {
			img.style.width = viewportWidth + 'px'; // Each image width is equal to the viewport width
		});
	};

	window.addEventListener('resize', updateSlideWidth);
	updateSlideWidth(); // Initial call to set the slide width on page load

	function slide(id) {
		const viewportWidth = window.innerWidth;
		imgContainer.style.transform = "translateX(" + (-viewportWidth * id) + "px)"; // Proper string formatting for transform
		pagination.forEach(pag => {
			pag.classList.remove('active');
		});
		pagination[id].classList.add('active');
	}

	let interval = setInterval(autoSlide, 3000);
	let imgId = 1;

	function autoSlide() {
		if (imgId > pagination.length - 1) {
			imgId = 0;
		}
		slide(imgId);
		imgId++;
	}

	pagination.forEach((dot, i) => {
		dot.addEventListener('click', () => {
			clearInterval(interval);
			slide(i);
			imgId = i + 1;
			interval = setInterval(autoSlide, 3000);
		});
	});

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
</body>

</html>