<?php
// Start session to access user_id and is_admin
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

// Fetch 'About Us' content
$about_us_result = $conn->query("SELECT content FROM about_us WHERE id=1");
$about_us_content = ($about_us_result && $about_us_result->num_rows > 0) ? $about_us_result->fetch_assoc()['content'] : "About Us content not available.";

// Fetch FAQs
$faqs_result = $conn->query("SELECT * FROM faqs");

// Assuming user_id is stored in session after login
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if user is an admin
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;

// Handle feedback submission
$feedback_message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback']) && $user_id !== null) {
    $rating = intval($_POST['rating']); // Get rating value (ensure it’s between 1 and 5)
    $feedback = $conn->real_escape_string($_POST['feedback']); // Escape feedback input for security

    // Validate rating before inserting
    if ($rating >= 1 && $rating <= 5) {
        $sql = "INSERT INTO feedbacks (user_id, rating, feedback) VALUES ($user_id, $rating, '$feedback')";
        if ($conn->query($sql)) {
            $feedback_message = "Thank you for your feedback!";
        } else {
            $feedback_message = "Error: Could not submit feedback.";
        }
    } else {
        $feedback_message = "Invalid rating. Please select a rating between 1 and 5.";
    }
} elseif ($user_id === null && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $feedback_message = "You must be logged in to submit feedback.";
}

// Fetch all feedback with the corresponding user's name or username
$feedbacks_result = $conn->query("
    SELECT f.id, f.rating, f.feedback, u.username, f.created_at, f.user_id 
    FROM feedbacks f 
    JOIN users u ON f.user_id = u.id
    ORDER BY f.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AmuseMe</title>
    <link rel="icon" href="./img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="style/fontawesome.css">
    <script src="./js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./style/Style.css">

<style>
        /* Feedback Section */
    .feedback-section {
    width: 55%;
    margin: auto; /* Center it and remove extra bottom space */
    padding: 20px;
    background-color: white;
    border-radius: 10px;
    }

    .star-rating {
    display: flex;
    font-size: 2em;
    color: lightgray;
    cursor: pointer;
    }

    .star-rating .star:hover,
    .star-rating .star.active {
    color: gold;
    }

    .feedback-section textarea {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    }


    .feedback-section button {
    padding: 10px 20px;
    margin-top: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    }

    .feedback-section button:hover {
    background-color: #45a049;
    }

    .feedback-success {
    color: green;
    }

    /* Feedback Display Styling */
    .feedbacks-container {
    margin-top: 40px;
    }


    .feedback {
    background-color: #fff;
    border: 1px solid #e0e0e0;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 8px;
    text-align: left;
    }


    .feedback h3 {
    font-size: 20px;
    margin-bottom: 10px;
    font-weight: bold;
    }


    .feedback p {
    margin: 5px 0;
    }


    .feedback form {
    display: inline-block;
    margin-right: 10px;
    }


    .feedback button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 8px 15px;
    cursor: pointer;
    border-radius: 4px;
    }

    .feedback button:hover {
    background-color: #218838;
    }


    .feedback form button {
    background-color: #dc3545;
    }


    .feedback form button:hover {
    background-color: #c82333;
    }
        .popup-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: lightgreen;
            color: black;
            padding: 15px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            font-size: 18px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        .popup-message.show {
            opacity: 1;
            visibility: visible;
        }
        .star-rating .star.active {
            color: gold;
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
    <?php include 'components/user_header.php'; ?>

    <div class="about-container">
        <section class="about">
            <img src="./img/logo.png" alt="AmuseMe Logo" class="logo-image">
            <h1>About Us</h1>
            <!-- Dynamic About Us Content -->
            <p><?php echo htmlspecialchars($about_us_content); ?></p>
        </section>

        <section class="about-faqs">
            <h2>FAQ's</h2>
            <div class="faq-container">
                <?php while ($faq = $faqs_result->fetch_assoc()) { ?>
                    <div class="faq">
                        <button class="faq-question" onclick="toggleAnswer(this)">
                            <?php echo htmlspecialchars($faq['question']); ?>
                            <span class="arrow">&#9660;</span>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    </div>

    <!-- Popup Message Element -->
    <div id="popupMessage" class="popup-message"></div>

    <!-- Feedback and Rating Section -->
    <section class="feedback-section">
        <h2>We value your feedback!</h2>
        <p>Please rate your experience with AmuseMe:</p>

        <form action="" method="POST">
            <input type="hidden" name="rating" id="rating-value" value="0">
            <div class="star-rating">
                <span class="star" onclick="rateExperience(1)">&#9733;</span>
                <span class="star" onclick="rateExperience(2)">&#9733;</span>
                <span class="star" onclick="rateExperience(3)">&#9733;</span>
                <span class="star" onclick="rateExperience(4)">&#9733;</span>
                <span class="star" onclick="rateExperience(5)">&#9733;</span>
            </div>

            <p id="rating-text"></p>

            <label for="feedback">Feedback/Comment:</label>
            <textarea name="feedback" id="feedback" rows="4" required></textarea>

            <button type="submit">Submit Feedback</button>
        </form>
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

    <script>
        // Function to handle star rating click and set the rating value
        function rateExperience(stars) {
            document.getElementById('rating-value').value = stars; // Update hidden input with selected rating
            const starElements = document.querySelectorAll('.star');
            starElements.forEach((star, index) => {
                if (index < stars) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
            document.getElementById('rating-text').innerText = `You rated ${stars} star(s).`;
        }

        // Function to show popup message with a fade-out effect
        function showPopupMessage(message) {
            const popup = document.getElementById('popupMessage');
            popup.textContent = message; // Set the message content
            popup.classList.add('show'); // Make popup visible

            // Hide the popup after 3 seconds
            setTimeout(() => {
                popup.classList.remove('show'); // Start fading out
            }, 3000); // Show for 3 seconds
        }

        // Display popup if there's a feedback message
        <?php if (!empty($feedback_message)) { ?>
            showPopupMessage("<?php echo $feedback_message; ?>");
        <?php } ?>

        // Function to toggle FAQ answer visibility
        function toggleAnswer(button) {
            const answer = button.nextElementSibling;
            const arrow = button.querySelector(".arrow");

            if (answer.style.maxHeight) {
                answer.style.maxHeight = null; // Close the answer
                arrow.classList.remove("rotate"); // Rotate the arrow back
            } else {
                answer.style.maxHeight = answer.scrollHeight + "px"; // Open the answer
                arrow.classList.add("rotate"); // Rotate the arrow down
            }
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

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>
</body>
</html>
