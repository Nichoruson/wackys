<?php
session_start();
include_once 'conn.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $eventType = trim($_POST['event_type'] ?? '');
    $eventDate = $_POST['event_date'] ?? '';
    $userId = $_SESSION['user_id'] ?? null;

    if ($fullName === '' || $phone === '' || $email === '' || $eventType === '' || $eventDate === '') {
        $error = 'Please complete all booking details.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO bookings (UserID, FullName, PhoneNumber, Email, EventType, EventDate) VALUES (?, ?, ?, ?, ?, ?)"
        );

        if ($stmt) {
            $stmt->bind_param("isssss", $userId, $fullName, $phone, $email, $eventType, $eventDate);

            if ($stmt->execute()) {
                $success = 'Your booking request has been saved successfully.';
            } else {
                $error = 'Booking could not be saved right now. Please try again.';
            }

            $stmt->close();
        } else {
            $error = 'Booking is unavailable right now. Please try again later.';
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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <title>Book a Reception</title>
    <link rel="stylesheet" href="book.css"> 
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="login-btn">
                    <i class='bx bx-user'></i> PROFILE
                </a>
                <a href="logout.php" class="login-btn" style="background: #e74c3c;">
                    <i class='bx bx-log-out'></i> LOGOUT
                </a>
            <?php else: ?>
                <a href="login.php" class="login-btn">
                    <i class='bx bx-user'></i> LOGIN
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Booking Section -->
    <div class="booking-section">
        <h2>Book a Reception</h2>

        <?php if (!empty($error)): ?>
            <p style="color:red; margin-bottom: 16px;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p style="color:green; margin-bottom: 16px;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="book.php" method="POST" class="booking-form">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Your Name" required>

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="Your Phone Number" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Your Email Address" required>

            <label for="event-type">Type of Event</label>
            <input type="text" id="event-type" name="event_type" placeholder="Type of Event" required>

            <label for="event-date">Event Date</label>
            <input type="date" id="event-date" name="event_date" required>

            <button type="submit">Book Now</button>
        </form>
    </div>


    <!-- FOOTER SECTION -->
    <footer>
        <div class="footer-content">
            <div class="contact-info">
                <h3>Contact Us</h3>
                <p>Email: <a href="mailto:contact@wackys.com">none@email.com</a></p>
                <p>Phone: <a href="tel:+123456789">+63 906 293 4699</a></p>
                <p>Address: Barangay Catioan, Capalonga Camarines Norte</p>
            </div>
            <div class="vertical-line"></div> 
            <div class="social-media">
                <h3>Follow Us</h3>
                <a href="https://facebook.com" target="_blank"><i class='bx bxl-facebook'></i> Facebook</a>
                <a href="https://instagram.com" target="_blank"><i class='bx bxl-instagram'></i> Instagram</a>
                <a href="https://twitter.com" target="_blank"><i class='bx bxl-twitter'></i> Twitter</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Wacky's Eat & Drink. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>
