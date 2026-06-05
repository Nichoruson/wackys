<?php
session_start();
include_once 'conn.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = (int) $_SESSION['user_id'];
$message = '';
$messageType = '';

function cartImagePath($imageName) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_quantity') {
        $cartId = (int) ($_POST['cart_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $stmt = $conn->prepare("UPDATE cart SET Quantity = ? WHERE CartID = ? AND UserID = ?");

        if ($stmt) {
            $stmt->bind_param("iii", $quantity, $cartId, $userId);
            $stmt->execute();
            $stmt->close();
            $message = 'Cart updated successfully.';
            $messageType = 'success';
        }
    } elseif ($action === 'remove_item') {
        $cartId = (int) ($_POST['cart_id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM cart WHERE CartID = ? AND UserID = ?");

        if ($stmt) {
            $stmt->bind_param("ii", $cartId, $userId);
            $stmt->execute();
            $stmt->close();
            $message = 'Item removed from your cart.';
            $messageType = 'success';
        }
    } elseif ($action === 'place_order') {
        $paymentMethod = trim($_POST['payment_method'] ?? 'Cash on Delivery');
        $cartQuery = "
            SELECT c.CartID, c.ProductID, c.Quantity, p.price
            FROM cart c
            INNER JOIN products p ON p.id = c.ProductID
            WHERE c.UserID = ?
        ";
        $cartStmt = $conn->prepare($cartQuery);

        if ($cartStmt) {
            $cartStmt->bind_param("i", $userId);
            $cartStmt->execute();
            $cartItemsResult = $cartStmt->get_result();
            $orderItems = [];
            $totalAmount = 0.0;

            while ($row = $cartItemsResult->fetch_assoc()) {
                $orderItems[] = $row;
                $totalAmount += ((float) $row['price']) * ((int) $row['Quantity']);
            }

            $cartStmt->close();

            if (empty($orderItems)) {
                $message = 'Your cart is empty.';
                $messageType = 'error';
            } else {
                $conn->begin_transaction();

                try {
                    $orderStmt = $conn->prepare(
                        "INSERT INTO orders (UserID, TotalAmount, OrderStatus, PaymentMethod) VALUES (?, ?, 'Pending', ?)"
                    );

                    if (!$orderStmt) {
                        throw new Exception('Could not create the order.');
                    }

                    $orderStmt->bind_param("ids", $userId, $totalAmount, $paymentMethod);
                    $orderStmt->execute();
                    $orderId = $orderStmt->insert_id;
                    $orderStmt->close();

                    $detailStmt = $conn->prepare(
                        "INSERT INTO orderdetails (OrderID, ProductID, Quantity, Price) VALUES (?, ?, ?, ?)"
                    );

                    if (!$detailStmt) {
                        throw new Exception('Could not save order details.');
                    }

                    foreach ($orderItems as $item) {
                        $productId = (int) $item['ProductID'];
                        $quantity = (int) $item['Quantity'];
                        $price = (float) $item['price'];
                        $detailStmt->bind_param("iiid", $orderId, $productId, $quantity, $price);
                        $detailStmt->execute();
                    }

                    $detailStmt->close();

                    $clearStmt = $conn->prepare("DELETE FROM cart WHERE UserID = ?");
                    if (!$clearStmt) {
                        throw new Exception('Could not clear the cart.');
                    }

                    $clearStmt->bind_param("i", $userId);
                    $clearStmt->execute();
                    $clearStmt->close();

                    $conn->commit();
                    header('Location: track.php?placed=1');
                    exit();
                } catch (Exception $e) {
                    $conn->rollback();
                    $message = 'Order placement failed. Please try again.';
                    $messageType = 'error';
                }
            }
        }
    }
}

$profileStmt = $conn->prepare("SELECT fullName, email, PhoneNumber, Address FROM users WHERE UserID = ?");
$userProfile = null;

if ($profileStmt) {
    $profileStmt->bind_param("i", $userId);
    $profileStmt->execute();
    $userProfile = $profileStmt->get_result()->fetch_assoc();
    $profileStmt->close();
}

$cartItems = [];
$cartTotal = 0.0;

$cartListQuery = "
    SELECT c.CartID, c.Quantity, p.id AS ProductID, p.name, p.price, p.image_path, p.description
    FROM cart c
    INNER JOIN products p ON p.id = c.ProductID
    WHERE c.UserID = ?
    ORDER BY c.DateAdded DESC
";
$cartListStmt = $conn->prepare($cartListQuery);

if ($cartListStmt) {
    $cartListStmt->bind_param("i", $userId);
    $cartListStmt->execute();
    $result = $cartListStmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $row['subtotal'] = ((float) $row['price']) * ((int) $row['Quantity']);
        $cartTotal += $row['subtotal'];
        $cartItems[] = $row;
    }

    $cartListStmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wacky's Cart</title>
    <link rel="stylesheet" href="cart.css">
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

    <!-- CART SECTION -->
    <section class="cart-section">
        <div class="container">
            <div class="cart-page-header">
                <p class="eyebrow">Real Cart Flow</p>
                <h1>Your Cart</h1>
                <p>Review your selected items, update quantities, and place your order.</p>
            </div>

            <?php if ($message !== ''): ?>
                <p class="flash-message <?php echo $messageType === 'success' ? 'flash-success' : 'flash-error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>

            <div class="cart-wrapper">
                <div class="cart-main">
                    <!-- ADDRESS SECTION -->
                    <div class="address-box">
                        <h2>Customer Details</h2>
                        <div class="address-details">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($userProfile['fullName'] ?? 'Not set yet'); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($userProfile['email'] ?? 'Not set yet'); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($userProfile['PhoneNumber'] ?? 'Not set yet'); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($userProfile['Address'] ?? 'Please add your address in the database profile first.'); ?></p>
                        </div>
                    </div>

                    <!-- CART ITEMS -->
                    <div class="cart-items">
                        <h2>Cart Items</h2>

                        <?php if (empty($cartItems)): ?>
                            <div class="empty-cart">
                                <p>Your cart is empty right now.</p>
                                <a href="menu.php" class="continue-btn">Browse Menu</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item">
                                    <img src="<?php echo htmlspecialchars(cartImagePath($item['image_path'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <div class="item-details">
                                        <p><strong><?php echo htmlspecialchars($item['name']); ?></strong></p>
                                        <p><?php echo htmlspecialchars($item['description'] ?: 'No description available yet.'); ?></p>
                                        <p>Unit Price: PHP <?php echo number_format((float) $item['price'], 2); ?></p>
                                        <p>Subtotal: PHP <?php echo number_format((float) $item['subtotal'], 2); ?></p>
                                        <div class="cart-actions">
                                            <form action="cart.php" method="POST" class="quantity-form">
                                                <input type="hidden" name="action" value="update_quantity">
                                                <input type="hidden" name="cart_id" value="<?php echo (int) $item['CartID']; ?>">
                                                <label for="quantity-<?php echo (int) $item['CartID']; ?>">Qty</label>
                                                <input
                                                    type="number"
                                                    id="quantity-<?php echo (int) $item['CartID']; ?>"
                                                    name="quantity"
                                                    min="1"
                                                    value="<?php echo (int) $item['Quantity']; ?>"
                                                >
                                                <button type="submit" class="update-btn">Update</button>
                                            </form>
                                            <form action="cart.php" method="POST">
                                                <input type="hidden" name="action" value="remove_item">
                                                <input type="hidden" name="cart_id" value="<?php echo (int) $item['CartID']; ?>">
                                                <button type="submit" class="remove-item">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- CART SUMMARY -->
                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <p><strong>Items:</strong> <?php echo count($cartItems); ?></p>
                    <p><strong>Total:</strong> PHP <?php echo number_format($cartTotal, 2); ?></p>
                    <form action="cart.php" method="POST" class="checkout-form">
                        <input type="hidden" name="action" value="place_order">
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method">
                            <option value="Cash on Delivery">Cash on Delivery</option>
                            <option value="Pickup Payment">Pickup Payment</option>
                        </select>
                        <button type="submit" class="confirm-order-btn" <?php echo empty($cartItems) ? 'disabled' : ''; ?>>
                            Place Order
                        </button>
                    </form>
                    <a href="menu.php" class="continue-btn secondary">Add More Items</a>
                </div>
            </div>
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
