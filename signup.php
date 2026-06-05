<?php
session_start();
include_once 'conn.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($fullName === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $checkStmt = $conn->prepare("SELECT UserID FROM users WHERE email = ?");

        if ($checkStmt) {
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $existingUser = $checkStmt->get_result();

            if ($existingUser && $existingUser->num_rows > 0) {
                $error = 'That email is already registered.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $insertStmt = $conn->prepare(
                    "INSERT INTO users (fullName, email, password) VALUES (?, ?, ?)"
                );

                if ($insertStmt) {
                    $insertStmt->bind_param("sss", $fullName, $email, $hashedPassword);

                    if ($insertStmt->execute()) {
                        $success = 'Account created successfully. You can log in now.';
                    } else {
                        $error = 'Could not create the account. Please try again.';
                    }

                    $insertStmt->close();
                } else {
                    $error = 'Signup is unavailable right now. Please try again later.';
                }
            }

            $checkStmt->close();
        } else {
            $error = 'Signup is unavailable right now. Please try again later.';
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wacky's Eat & Drink</title>
    <link rel="stylesheet" href="signup.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    <!--NAV-->
    <nav>
        <div class="logo">
            <img src="assets/wackys_Logo.png" alt="Wacky's Logo">
        </div>  
        <ul class="nav-links">
            <li><a href="dashboard.php">HOME</a></li>
            <li><a href="menu.php">MENU</a></li>
            <li><a href="book.php">BOOK A RECEPTION</a></li>
            <li><a href="location.php">VISIT US</a></li>
        </ul>
        <div class="nav-buttons">
            <a href="cart.php" class="cart-btn">
                <i class='bx bx-cart'></i> Cart
            </a>
            <a href="track.php" class="order-btn">
                <i class='bx bx-package'></i> Track Order
            </a>
            <a href="login.php" class="login-btn">
                <i class='bx bx-user'></i> LOGIN
            </a>
        </div>
    </nav>



    <!--LOGIN FORM-->
<section class="login-section">
    <div class="login-box">
        <h2>Create Your Account</h2>
        <p class="form-intro">Save your details so you can log in, track orders, and manage bookings.</p>

        <?php if (!empty($error)): ?>
            <p style="color:red; text-align:center;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p style="color:green; text-align:center;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="signup.php" method="POST">
            <div class="input-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            <button type="submit" class="login-submit">Create Account</button>
        </form>
        <p class="signup-text">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</section>




   </body>
</html>
