<?php
session_start();
include_once 'conn.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter both email and password.";
    } else {
        $sql = "SELECT UserID, fullName, email, password, Role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();

                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['UserID'];
                    $_SESSION['fullname'] = $row['fullName'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['role'] = $row['Role'];

                    header("Location: dashboard.php");
                    exit();
                }

                $error = "Incorrect password.";
            } else {
                $error = "Email not found.";
            }

            $stmt->close();
        } else {
            $error = "Login is unavailable right now. Please try again later.";
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
    <link rel="stylesheet" href="login.css">
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

<!-- LOGIN FORM -->
<section class="login-section">
    <div class="login-box">
        <h2>Welcome Back!</h2>

        <?php if (!empty($error)): ?>
            <p style="color:red; text-align:center;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="login-submit">Login</button>
        </form>
        <p class="signup-text">Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
</section>

</body>
</html>
