<?php include('components/connect.php'); 

?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="./style/Style.css">
</head>
<body>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <h2>Forgot Password</h2>
            
            <!-- Display validation errors -->
            <?php if (count($errors) > 0): ?>
                <div class="error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <button type="submit" name="forgot_password">Send Reset Link</button>

            <p><a href="signin.php">Back to Sign In</a></p>
        </form>
    </div>
</body>
</html>
