<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Us - Wacky's Eat & Drink</title>
    <link rel="stylesheet" href="location.css">
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

    <!-- LOCATION SECTION -->
    <section class="location-section">
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3865.7158606736207!2d122.49502677481408!3d14.327930283694814!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33988d5b67aaa459%3A0xa458c45bb4ece4e6!2sWacky&#39;s%20Store%20%2F%20Wacky&#39;s%20Eat%20%26%20Drink!5e0!3m2!1sen!2sph!4v1745338308924!5m2!1sen!2sph" 
            width="800" height="600" 
            style="border:0;" allowfullscreen="" 
            loading="lazy" referrerpolicy="no-referrer-when-downgrade">

            </iframe>
        </div>

        <div class="contact-info">
            <h2>Contact Us</h2>
            <p><span class="contact-icons"><i class='bx bxs-phone'></i></span>+63 906 293 4699</p>
            <p><span class="contact-icons"><i class='bx bxs-envelope'></i></span>none@email.com</p>
            <p><span class="contact-icons"><i class='bx bxs-time'></i></span>6:00 AM - 11:00 PM</p>
            <p><span class="contact-icons"><i class='bx bxs-map'></i></span>Barangay Catioan, Capalonga Camarines Norte</p>
        </div>
    </section>

</body>
</html>
