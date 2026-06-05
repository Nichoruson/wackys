<?php
session_start();
include_once 'conn.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = (int) $_SESSION['user_id'];
$placedMessage = isset($_GET['placed']) ? 'Your order was placed successfully.' : '';
$orders = [];

function trackImagePath($imageName) {
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

$orderQuery = "
    SELECT o.OrderID, o.OrderDate, o.TotalAmount, o.OrderStatus, o.PaymentMethod,
           od.Quantity, od.Price, p.name, p.image_path
    FROM orders o
    INNER JOIN orderdetails od ON od.OrderID = o.OrderID
    INNER JOIN products p ON p.id = od.ProductID
    WHERE o.UserID = ?
    ORDER BY o.OrderDate DESC, o.OrderID DESC, od.OrderDetailID ASC
";
$orderStmt = $conn->prepare($orderQuery);

if ($orderStmt) {
    $orderStmt->bind_param("i", $userId);
    $orderStmt->execute();
    $result = $orderStmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $orderId = (int) $row['OrderID'];

        if (!isset($orders[$orderId])) {
            $orders[$orderId] = [
                'OrderID' => $orderId,
                'OrderDate' => $row['OrderDate'],
                'TotalAmount' => $row['TotalAmount'],
                'OrderStatus' => $row['OrderStatus'],
                'PaymentMethod' => $row['PaymentMethod'],
                'items' => []
            ];
        }

        $orders[$orderId]['items'][] = [
            'name' => $row['name'],
            'image_path' => $row['image_path'],
            'quantity' => $row['Quantity'],
            'price' => $row['Price']
        ];
    }

    $orderStmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking your Order</title>
    <link rel="stylesheet" href="track.css">
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


<section class="track-order-section">
    <div class="container">
        <div class="page-heading">
            <p class="eyebrow">Live Order History</p>
            <h1>Track Your Orders</h1>
            <p>View the order status, payment method, and line items saved in your database.</p>
        </div>

        <?php if ($placedMessage !== ''): ?>
            <p class="flash-message flash-success"><?php echo htmlspecialchars($placedMessage); ?></p>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <p>You have not placed any orders yet.</p>
                <a href="menu.php" class="browse-btn">Start Ordering</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <section class="order-group">
                    <div class="order-summary">
                        <div>
                            <p class="summary-label">Order #<?php echo (int) $order['OrderID']; ?></p>
                            <h2><?php echo htmlspecialchars($order['OrderStatus']); ?></h2>
                        </div>
                        <div class="summary-grid">
                            <p><strong>Date:</strong> <?php echo htmlspecialchars(date('F j, Y g:i A', strtotime($order['OrderDate']))); ?></p>
                            <p><strong>Payment:</strong> <?php echo htmlspecialchars($order['PaymentMethod'] ?: 'Not provided'); ?></p>
                            <p><strong>Total:</strong> PHP <?php echo number_format((float) $order['TotalAmount'], 2); ?></p>
                            <p><strong>Status:</strong> <span class="status <?php echo strtolower(htmlspecialchars($order['OrderStatus'])); ?>"><?php echo htmlspecialchars($order['OrderStatus']); ?></span></p>
                        </div>
                    </div>

                    <?php foreach ($order['items'] as $item): ?>
                        <div class="order-card">
                            <img src="<?php echo htmlspecialchars(trackImagePath($item['image_path'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="order-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p>Quantity: <?php echo (int) $item['quantity']; ?></p>
                                <p>Price Each: PHP <?php echo number_format((float) $item['price'], 2); ?></p>
                                <p>Line Total: PHP <?php echo number_format(((float) $item['price']) * ((int) $item['quantity']), 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endforeach; ?>
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
