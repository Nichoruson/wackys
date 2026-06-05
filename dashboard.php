<?php
session_start();
include_once 'conn.php';

$featuredItems = [];

function dashboardImagePath($imageName) {
    if (!$imageName) {
        return 'assets/wackys_Logo.png';
    }

    $candidate = 'assets/' . $imageName;
    $lowercaseCandidate = 'assets/' . strtolower($imageName);

    if (file_exists(__DIR__ . '/' . $candidate)) {
        return $candidate;
    }

    if (file_exists(__DIR__ . '/' . $lowercaseCandidate)) {
        return $lowercaseCandidate;
    }

    return 'assets/wackys_Logo.png';
}

$result = $conn->query(
    "SELECT name, price, image_path FROM products WHERE isAvailable = 'Y' ORDER BY id ASC LIMIT 4"
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $featuredItems[] = $row;
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
    <link rel="stylesheet" href="dashboard.css">
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

    <section class="intro-banner">
        <div class="intro-content">
            <p class="eyebrow">Fresh Meals, Family Gatherings, Fast Orders</p>
            <h1>Welcome to Wacky's Eat & Drink!</h1>
            <p>Order your favorites, book special events, and visit the store with one simple flow.</p>
            <div class="hero-actions">
                <a href="menu.php" class="primary-action">Explore Menu</a>
                <a href="book.php" class="secondary-action">Book a Reception</a>
            </div>
        </div>
    </section>

    <section class="highlights">
        <div class="highlight-card">
            <h2>Quick Ordering</h2>
            <p>Browse categories faster and head straight from menu to cart.</p>
        </div>
        <div class="highlight-card">
            <h2>Reception Booking</h2>
            <p>Reserve your event online and keep your request details saved in the database.</p>
        </div>
        <div class="highlight-card">
            <h2>Store Visit Info</h2>
            <p>Check the location, contact details, and opening hours before you go.</p>
        </div>
    </section>

    <!-- MENU CARDS-->
    <section class="menu-section">
        <h1>Featured Picks</h1>
        <div class="menu-grid">
            <?php if (!empty($featuredItems)): ?>
                <?php foreach ($featuredItems as $item): ?>
                    <div class="menu-card">
                        <img src="<?php echo htmlspecialchars(dashboardImagePath($item['image_path'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p>Starts at PHP <?php echo number_format((float) $item['price'], 2); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="menu-card">
                    <img src="assets/burger.jpg" alt="House special">
                    <h3>Our favorites are loading</h3>
                    <p>Visit the menu page to explore the full selection.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

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
