<?php
require_once 'config.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

// Get cart items
$stmt = $pdo->prepare("SELECT c.id as cart_id, c.quantity, p.* 
                       FROM cart c JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Redirect if cart is empty
if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $total]);
    $order_id = $pdo->lastInsertId();
    
    // Add order items
    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
        
        // Update product stock
        $new_stock = $item['stock_quantity'] - $item['quantity'];
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?");
        $stmt->execute([$new_stock, $item['id']]);
    }
    
    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    // Redirect to order confirmation
    header("Location: order_confirmation.php?order_id=$order_id");
    exit;
}

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$page_title = 'Checkout - Bitronics';
?>

<?php include 'header.php'; ?>

<main class="section-container">
    <h1 class="section-title">Checkout</h1>
    
    <form action="checkout.php" method="post">
        <div class="checkout-container">
            <div class="checkout-form">
                <h2 class="form-section-title">Shipping Information</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" 
                               value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="zip_code">ZIP Code</label>
                        <input type="text" id="zip_code" name="zip_code" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="country">Country</label>
                    <select id="country" name="country" required>
                        <option value="Philippines" selected>Philippines</option>
                        <option value="United States">United States</option>
                        <option value="Canada">Canada</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Australia">Australia</option>
                    </select>
                </div>
                
                <div class="payment-methods">
                    <h2 class="form-section-title">Payment Method</h2>
                    
                    <div class="payment-options">
                        <label class="payment-method selected">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <div class="payment-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="payment-details">
                                <span class="payment-name">Cash on Delivery (COD)</span>
                                <span class="payment-description">Pay when you receive your order</span>
                            </div>
                        </label>
                        
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="credit_card">
                            <div class="payment-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="payment-details">
                                <span class="payment-name">Credit/Debit Card</span>
                                <span class="payment-description">Pay securely with your card</span>
                            </div>
                        </label>
                        
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="gcash">
                            <div class="payment-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="payment-details">
                                <span class="payment-name">GCash</span>
                                <span class="payment-description">Pay via GCash mobile wallet</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="checkout-summary">
                <h2 class="form-section-title">Order Summary</h2>
                
                <div class="order-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <div class="item-image">
                                <img src="<?php echo htmlspecialchars($item['product_image'] ?? 'https://via.placeholder.com/100'); ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                            </div>
                            <div class="item-details">
                                <h3 class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                <div class="item-quantity">Quantity: <?php echo $item['quantity']; ?></div>
                            </div>
                            <div class="item-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-totals">
                    <div class="order-total-row">
                        <span>Subtotal</span>
                        <span>₱<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="order-total-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="order-total-row grand-total">
                        <span>Total</span>
                        <span>₱<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
                
                <button type="submit" class="place-order-btn">
                    Place Order
                </button>
            </div>
        </div>
    </form>
</main>

<style>
    .checkout-container {
        display: flex;
        gap: 30px;
        margin-top: 20px;
    }
    
    .checkout-form {
        flex: 2;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 25px;
    }
    
    .checkout-summary {
        flex: 1;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 25px;
    }
    
    .form-section-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 18px;
        margin-bottom: 20px;
        color: var(--primary);
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--primary);
    }
    
    .form-group input, 
    .form-group select, 
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 4px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .form-group input:focus, 
    .form-group select:focus, 
    .form-group textarea:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 2px rgba(15, 47, 77, 0.1);
    }
    
    .form-row {
        display: flex;
        gap: 20px;
    }
    
    .form-row .form-group {
        flex: 1;
    }
    
    .payment-methods {
        margin-top: 30px;
    }
    
    .payment-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .payment-method {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border: 1px solid var(--border);
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .payment-method.selected {
        border-color: var(--primary);
        background-color: rgba(15, 47, 77, 0.05);
    }
    
    .payment-method input {
        display: none;
    }
    
    .payment-icon {
        font-size: 24px;
        color: var(--primary);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(15, 47, 77, 0.1);
        border-radius: 50%;
    }
    
    .payment-details {
        flex: 1;
    }
    
    .payment-name {
        display: block;
        font-weight: 600;
        color: var(--primary);
    }
    
    .payment-description {
        display: block;
        font-size: 12px;
        color: var(--light-text);
    }
    
    .order-items {
        margin-bottom: 20px;
    }
    
    .order-item {
        display: flex;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid var(--border);
    }
    
    .item-image {
        width: 60px;
        height: 60px;
    }
    
    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 4px;
    }
    
    .item-details {
        flex: 1;
    }
    
    .item-name {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 5px;
        color: var(--primary);
    }
    
    .item-quantity {
        font-size: 12px;
        color: var(--light-text);
    }
    
    .item-price {
        font-weight: 600;
        color: var(--primary);
    }
    
    .order-totals {
        margin-top: 20px;
    }
    
    .order-total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .grand-total {
        font-weight: 600;
        font-size: 18px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border);
    }
    
    .place-order-btn {
        width: 100%;
        padding: 15px;
        background-color: var(--primary);
        color: white;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        font-family: 'Orbitron', sans-serif;
        cursor: pointer;
        margin-top: 20px;
        transition: all 0.3s;
    }
    
    .place-order-btn:hover {
        background-color: var(--primary-light);
    }
    
    @media (max-width: 768px) {
        .checkout-container {
            flex-direction: column;
        }
        
        .form-row {
            flex-direction: column;
            gap: 0;
        }
    }
</style>

<script>
    // Payment method selection
    document.querySelectorAll('.payment-method').forEach(method => {
        method.addEventListener('click', function() {
            document.querySelectorAll('.payment-method').forEach(m => {
                m.classList.remove('selected');
            });
            this.classList.add('selected');
            this.querySelector('input').checked = true;
        });
    });
</script>

<?php include 'footer.php'; ?>