<?php include('components/connect.php'); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Home | Register</title>
    <link rel="icon" href="./img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="fontawesome.css">
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
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                <!-- Display validation errors here -->
                <?php if (count($errors) > 0): ?>
                    <div class="error">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

                <label for="contact_no">Contact Number</label>
                <input type="text" id="contact_no" name="contact_no" value="<?php echo isset($contact_no) ? htmlspecialchars($contact_no) : ''; ?>" required>

                <label for="password_1">Password</label>
                <div class="password-container">
                    <input type="password" id="password_1" name="password_1" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('password_1', this)"></i>
                </div>

                <label for="password_2">Confirm Password</label>
                <div class="password-container">
                    <input type="password" id="password_2" name="password_2" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('password_2', this)"></i>
                </div>

                <button type="submit" name="reg_user">Register</button>

                <p>Already a member? <a href="signin.php">Sign in</a></p>
            </form>
        </div>
    </div>

    <!-- Footer START -->
    <?php include 'components/footer.php'; ?>
    <!-- Footer END -->

    <script>
        function togglePassword(fieldId, iconElement) {
            const passwordInput = document.getElementById(fieldId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                iconElement.classList.remove('fa-eye');
                iconElement.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                iconElement.classList.remove('fa-eye-slash');
                iconElement.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
