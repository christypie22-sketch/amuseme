<?php
session_start();
if (!isset($_SESSION['success_message'])) {
    header("Location: user_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful | Amuse Me</title>
    <link rel="stylesheet" href="./style/Style.css">
</head>
<body>
    <section class="order-success">
        <h3>Thank you for your purchase!</h3>
        <p><?php echo $_SESSION['success_message']; ?></p>
        <a href="user_profile.php">Go back to profile</a>
    </section>
</body>
</html>

<?php
unset($_SESSION['success_message']);
?>
