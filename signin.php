<?php include('components/connect.php'); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Home | Login</title>
    <link rel="icon" href="./img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href=".style/fontawesome.css">
    <link rel="stylesheet" href="./style/Style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="./js/fontawesome.js" crossorigin="anonymous"></script>
    <style>
        .password-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        .password-container input {
            width: 100%;
            padding-right: 40px; /* Space for the icon */
        }
        .password-container .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- HEADER START -->
    <?php include 'components/user_header.php'; ?>
    <!-- HEADER END -->

    <div class="form-container">
        <div class="form-image-section">
            <div class="quote">
                <h2><strong>Register now!</strong></h2>
                <h1>Your gateway to unforgettable experiences.</h1>
            </div>
            <img src="./img/bg2.png" alt="Background Image">
        </div>

        <div class="form-section">
            <!-- Display success/error message from session if available -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-info">
                    <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']); // Clear the message after displaying
                    ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                </div>

                <a href="forgot_password.php">Forgot password?</a>

                <button type="submit" name="login_user">Log In</button>

                <p>Don't have an account? <a href="register.php">Sign Up</a></p>
            </form>
        </div>
    </div>

    <!-- Footer START -->
    <?php include 'components/footer.php'; ?>
    <!-- Footer END -->

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
