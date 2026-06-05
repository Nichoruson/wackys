<?php
session_start();
include_once 'conn.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST['fullName'] ?? '');
    $phoneNumber = trim($_POST['phoneNumber'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($fullName === '') {
        $error_msg = "Full name is required.";
    } else {
        $sql = "UPDATE users SET fullName = ?, PhoneNumber = ?, Address = ? WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssi", $fullName, $phoneNumber, $address, $user_id);
            if ($stmt->execute()) {
                $success_msg = "Profile updated successfully!";
                $_SESSION['fullname'] = $fullName; // Update session name
            } else {
                $error_msg = "Error updating profile. Please try again.";
            }
            $stmt->close();
        } else {
            $error_msg = "Database error. Please try again later.";
        }
    }
}

// Fetch current user data
$sql = "SELECT fullName, email, PhoneNumber, Address FROM users WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Wacky's Eat & Drink</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="profile.css">
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
                <a href="profile.php" class="login-btn active">
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

    <section class="profile-section">
        <div class="profile-container">
            <div class="profile-header">
                <h1>Your Profile</h1>
                <p>Update your personal information below.</p>
            </div>

            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form action="profile.php" method="POST" class="profile-form">
                <div class="form-group">
                    <label for="email">Email Address (Read-only)</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <small>Email cannot be changed.</small>
                </div>

                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($user['fullName']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phoneNumber">Phone Number</label>
                    <input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($user['PhoneNumber']); ?>" placeholder="e.g. 09123456789">
                </div>

                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" rows="3" placeholder="Enter your complete address"><?php echo htmlspecialchars($user['Address']); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </section>

    <footer style="text-align: center; padding: 2rem; background: #f9f9f9; margin-top: 2rem; color: #666;">
        <p>&copy; 2026 Wacky's Eat & Drink. All rights reserved.</p>
    </footer>

</body>
</html>
