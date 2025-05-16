<?php
require_once 'config.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=cart.php');
    exit;
}

// Handle add to cart request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Check if product already in cart
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    $existing_item = $stmt->fetch();
    
    if ($existing_item) {
        // Update quantity
        $new_quantity = $existing_item['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $existing_item['id']]);
    } else {
        // Add new item
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
    }
    
    header('Location: cart.php');
    exit;
}

// Handle remove from cart request
if (isset($_GET['remove'])) {
    $cart_id = $_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
    header('Location: cart.php');
    exit;
}

// Handle update quantity request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$quantity, $cart_id, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $_SESSION['user_id']]);
    }
    
    header('Location: cart.php');
    exit;
}

// Get cart items with product details
$stmt = $pdo->prepare("SELECT c.id as cart_id, c.quantity, p.* 
                       FROM cart c JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Bitronics</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #0f2f4d;
            --primary-light: #1a3d5f;
            --secondary: #d9e6eb;
            --accent: #3a86ff;
            --text: #333;
            --light-text: #777;
            --border: #e0e0e0;
            --error: #e63946;
            --success: #2a9d8f;
            --white: #fff;
            --gray: #f5f5f5;
        }
        
        .cart-container {
            margin-top: 30px;
        }
        
        .cart-items {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .cart-item {
            display: flex;
            gap: 20px;
            padding: 15px 0;
            border-bottom: 1px solid var(--border);
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border-radius: 4px;
        }
        
        .cart-item-details {
            flex: 1;
        }
        
        .cart-item-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .cart-item-price {
            font-weight: 500;
            color: var(--text);
            margin-bottom: 10px;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .cart-item-quantity input {
            width: 60px;
            padding: 5px;
            text-align: center;
            border: 1px solid var(--border);
            border-radius: 4px;
        }
        
        .cart-item-remove {
            color: var(--error);
            text-decoration: none;
            font-size: 14px;
        }
        
        .cart-summary {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .cart-summary-total {
            font-weight: 600;
            font-size: 18px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
        }
        
        .checkout-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        .checkout-btn:hover {
            background-color: var(--primary-light);
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart i {
            font-size: 50px;
            color: var(--light-text);
            margin-bottom: 20px;
        }
        
        .empty-cart p {
            margin-bottom: 20px;
            color: var(--light-text);
        }
        
        .continue-shopping {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
            }
            
            .cart-item-image {
                width: 100%;
                height: auto;
                max-height: 200px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="section-container">
        <h2 class="section-title">Your Shopping Cart</h2>
        
        <div class="cart-container">
            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added anything to your cart yet</p>
                    <a href="products.php" class="continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($item['product_image'] ?? 'https://via.placeholder.com/100'); ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                 class="cart-item-image">
                            <div class="cart-item-details">
                                <h3 class="cart-item-title"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                <div class="cart-item-price">₱<?php echo number_format($item['price'], 2); ?></div>
                                <form action="cart.php" method="post" class="cart-item-quantity">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <label for="quantity">Qty:</label>
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                                    <button type="submit" name="update_quantity" style="background: none; border: none; cursor: pointer;">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                                <a href="cart.php?remove=<?php echo $item['cart_id']; ?>" class="cart-item-remove">
                                    <i class="fas fa-trash"></i> Remove
                                </a>
                            </div>
                            <div style="font-weight: 600;">
                                ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <div class="cart-summary-row">
                        <span>Subtotal</span>
                        <span>₱<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="cart-summary-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="cart-summary-row cart-summary-total">
                        <span>Total</span>
                        <span>₱<?php echo number_format($total, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        // Update cart count in header
        function updateCartCount(count) {
            document.querySelectorAll('.cart-count').forEach(element => {
                element.textContent = count;
            });
        }
        
        // Handle quantity changes
        document.querySelectorAll('input[name="quantity"]').forEach(input => {
            input.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>