<?php
session_start();
include_once 'conn.php';

$categories = [];
$products = [];
$activeCategory = '';
$feedbackMessage = '';
$feedbackType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_to_cart') {
    if (empty($_SESSION['user_id'])) {
        $feedbackMessage = 'Please log in first before adding items to your cart.';
        $feedbackType = 'error';
    } else {
        $userId = (int) $_SESSION['user_id'];
        $productId = (int) ($_POST['product_id'] ?? 0);

        if ($productId > 0) {
            $checkStmt = $conn->prepare("SELECT CartID, Quantity FROM cart WHERE UserID = ? AND ProductID = ?");

            if ($checkStmt) {
                $checkStmt->bind_param("ii", $userId, $productId);
                $checkStmt->execute();
                $existingItem = $checkStmt->get_result()->fetch_assoc();
                $checkStmt->close();

                if ($existingItem) {
                    $newQuantity = (int) $existingItem['Quantity'] + 1;
                    $updateStmt = $conn->prepare("UPDATE cart SET Quantity = ? WHERE CartID = ?");

                    if ($updateStmt) {
                        $cartId = (int) $existingItem['CartID'];
                        $updateStmt->bind_param("ii", $newQuantity, $cartId);
                        $updateStmt->execute();
                        $updateStmt->close();
                    }
                } else {
                    $insertStmt = $conn->prepare("INSERT INTO cart (UserID, ProductID, Quantity) VALUES (?, ?, 1)");

                    if ($insertStmt) {
                        $insertStmt->bind_param("ii", $userId, $productId);
                        $insertStmt->execute();
                        $insertStmt->close();
                    }
                }

                $feedbackMessage = 'Item added to your cart.';
                $feedbackType = 'success';
            } else {
                $feedbackMessage = 'Cart is unavailable right now. Please try again.';
                $feedbackType = 'error';
            }
        } else {
            $feedbackMessage = 'Invalid menu item selected.';
            $feedbackType = 'error';
        }
    }
}

function menuImagePath($imageName) {
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

$categoryResult = $conn->query("SELECT category_id, category_name FROM category ORDER BY category_id ASC");
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

if (!empty($categories)) {
    $activeCategory = $categories[0]['category_name'];
}

$productQuery = "
    SELECT p.id, p.name, p.price, p.description, p.image_path, c.category_name
    FROM products p
    LEFT JOIN category c ON c.category_id = p.category_id
    WHERE p.isAvailable = 'Y'
    ORDER BY c.category_id ASC, p.id ASC
";
$productResult = $conn->query($productQuery);

if ($productResult) {
    while ($row = $productResult->fetch_assoc()) {
        $products[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu | Wacky's Eat & Drink</title>
  <link rel="stylesheet" href="menu.css">
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

  <section class="menu-page">
    <div class="page-heading">
      <p class="eyebrow">Database-Driven Menu</p>
      <h1>Our Menu</h1>
      <p class="page-copy">These categories and items are loaded from the `category` and `products` tables in your SQL file.</p>
    </div>

    <?php if ($feedbackMessage !== ''): ?>
      <p class="flash-message <?php echo $feedbackType === 'success' ? 'flash-success' : 'flash-error'; ?>">
        <?php echo htmlspecialchars($feedbackMessage); ?>
      </p>
    <?php endif; ?>

    <div class="menu-controls">
      <input type="text" id="menuSearch" placeholder="Search for a dish...">
      <div class="category-tabs">
        <?php foreach ($categories as $index => $category): ?>
          <button
            class="tab <?php echo $index === 0 ? 'active' : ''; ?>"
            data-category="<?php echo htmlspecialchars($category['category_name']); ?>"
            type="button"
          >
            <?php echo htmlspecialchars($category['category_name']); ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="menu-items" id="menuItems">
      <?php foreach ($products as $product): ?>
        <article
          class="menu-card"
          data-category="<?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>"
          data-name="<?php echo htmlspecialchars(strtolower($product['name'])); ?>"
        >
          <img src="<?php echo htmlspecialchars(menuImagePath($product['image_path'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
          <div class="menu-card-body">
            <p class="menu-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p><?php echo htmlspecialchars($product['description'] ?: 'No description available yet.'); ?></p>
            <span>PHP <?php echo number_format((float) $product['price'], 2); ?></span>
            <form action="menu.php" method="POST" class="add-to-cart-form">
              <input type="hidden" name="action" value="add_to_cart">
              <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
              <button type="submit" class="add-to-cart-btn">Add to Cart</button>
            </form>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <p class="empty-state" id="emptyState">No menu items match your current search.</p>
  </section>

  <script src="menu.js"></script>

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
