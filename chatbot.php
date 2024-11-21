<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot - Messenger Style with Predefined Questions</title>
    <link rel="icon" href="./img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="fontawesome.css">
    <link rel="stylesheet" href="./style/Style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        .chatbot-container {
            display: flex;
            justify-content: space-between;
            width: 70%;
            margin: 50px auto;
            border: 1px solid #ccc;
            border-radius: 10px;
            overflow: hidden;
        }

        /* Left side chatbox */
        .chatbox {
            width: 60%;
            border-right: 1px solid #ccc;
        }

        .chat-messages {
            height: 300px;
            padding: 20px;
            overflow-y: auto;
            background-color: #f1f1f1;
            display: flex;
            flex-direction: column;
        }

        .message {
            padding: 10px;
            border-radius: 10px;
            margin: 5px 0;
        }

        .user-message {
            background-color: #d1e7ff;
            align-self: flex-end;
        }

        .bot-message {
            background-color: #e8e8e8;
            align-self: flex-start;
        }

        .chat-input {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #ccc;
            background-color: white;
        }

        .chat-input input[type="text"] {
            width: 80%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .chat-input input[type="submit"] {
            width: 15%;
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Right side predefined questions */
        .question-buttons {
            width: 35%;
            padding: 20px;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            max-height: 350px; /* Set a maximum height */
            overflow-y: auto; /* Enable vertical scrolling */
        }

        .question-buttons h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .question-buttons button {
            background-color: #007bff;
            color: white;
            padding: 15px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .question-buttons button:hover {
            background-color: #0056b3;
        }

        /* Scrollbar styling for better UX */
        .question-buttons::-webkit-scrollbar {
            width: 8px;
        }

        .question-buttons::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .question-buttons::-webkit-scrollbar-thumb {
            background-color: #007bff;
            border-radius: 10px;
        }

        /* Responsive design */
        @media only screen and (max-width: 768px) {
            .chatbot-container {
                flex-direction: column;
            }

            .chatbox, .question-buttons {
                width: 100%;
            }

            .question-buttons {
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'components/user_header.php'; ?><br>
<center><h2>CHAT SUPPORT SYSTEM</h2></center>
    <div class="chatbot-container" style="margin-top: 20px;">
        <!-- Chatbox Section -->
        <div class="chatbox">
            <div class="chat-messages" id="chatMessages">
                <!-- Chat messages will appear here -->
            </div>
            <div class="chat-input">
                <input type="text" id="userMessage" placeholder="Type your message...">
                <input type="submit" value="Send" id="sendMessage">
            </div>
        </div>

        <!-- Predefined Questions Section with Scroll -->
        <div class="question-buttons" id="questionButtons">
            <!-- Question buttons will be loaded here -->
        </div>
    </div>

    <!-- Footer START -->
    <?php include 'components/footer.php'; ?>
    <!-- Footer END -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load predefined questions
            loadQuestions();

            // Handle the send button click
            $('#sendMessage').click(function() {
                sendMessage($('#userMessage').val().trim());
            });

            // Function to send message
            function sendMessage(userMessage) {
                if (userMessage !== '') {
                    // Display the user's message in the chat window
                    $('#chatMessages').append('<div class="message user-message">' + userMessage + '</div>');

                    // Clear the input field
                    $('#userMessage').val('');

                    // Send the message to the server via AJAX
                    $.ajax({
                        url: 'chatbot_backend.php',
                        method: 'POST',
                        data: { message: userMessage },
                        success: function(response) {
                            // Display bot's response
                            $('#chatMessages').append('<div class="message bot-message">' + response + '</div>');

                            // Scroll to the bottom of the chat
                            $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                        }
                    });
                }
            }

            // Load questions from the server
            function loadQuestions() {
                $.ajax({
                    url: 'fetch_questions.php', // Backend script to fetch the questions
                    method: 'GET',
                    success: function(response) {
                        $('#questionButtons').html(response);

                        // Attach click event to each button
                        $('.question-btn').click(function() {
                            const question = $(this).text();
                            sendMessage(question);
                        });
                    }
                });
            }
        });
    </script>
</body>
</html>
