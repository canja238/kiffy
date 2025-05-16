<?php
require_once 'config.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    header('Location: orders.php');
    exit;
}

$order_id = $_GET['order_id'];

// Get order details
$stmt = $pdo->prepare("SELECT o.*, u.username, u.email 
                       FROM orders o JOIN users u ON o.user_id = u.id 
                       WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Get order items
$stmt = $pdo->prepare("SELECT oi.*, p.product_name, p.product_image 
                       FROM order_items oi JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

// Get cart count for header
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cart_count = $stmt->fetch()['count'];

$page_title = 'Order Confirmation - Bitronics';
?>

<?php include 'header.php'; ?>

<main class="section-container">
    <div class="confirmation-card">
        <div class="confirmation-header">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="confirmation-title">Order Confirmed!</h1>
            <p class="confirmation-subtitle">Thank you for your purchase. Your order has been received.</p>
        </div>
        
        <div class="order-details">
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span class="detail-value">#<?php echo $order['id']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value"><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value"><?php echo htmlspecialchars($order['email']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total:</span>
                <span class="detail-value total-price">₱<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>
        
        <div class="order-items">
            <h2 class="section-subtitle">Order Items</h2>
            <?php foreach ($items as $item): ?>
                <div class="order-item">
                    <div class="item-image">
                        <?php if ($item['product_image']): ?>
                            <img src="<?php echo htmlspecialchars($item['product_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                        <?php else: ?>
                            <div class="placeholder-image">
                                <i class="fas fa-box-open"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="item-info">
                        <h3 class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                        <div class="item-meta">
                            <span class="item-price">₱<?php echo number_format($item['price'], 2); ?></span>
                            <span class="item-quantity">× <?php echo $item['quantity']; ?></span>
                            <span class="item-subtotal">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="confirmation-actions">
            <a href="products.php" class="btn continue-shopping-btn">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
            <a href="orders.php" class="btn view-orders-btn">
                <i class="fas fa-clipboard-list"></i> View Orders
            </a>
        </div>
    </div>
</main>

<style>
    .confirmation-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 30px;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .confirmation-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border);
    }
    
    .confirmation-icon {
        font-size: 60px;
        color: var(--success);
        margin-bottom: 15px;
    }
    
    .confirmation-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 28px;
        margin-bottom: 10px;
        color: var(--primary);
    }
    
    .confirmation-subtitle {
        font-size: 16px;
        color: var(--light-text);
    }
    
    .order-details {
        background-color: rgba(15, 47, 77, 0.05);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .detail-row:last-child {
        margin-bottom: 0;
    }
    
    .detail-label {
        font-weight: 500;
        color: var(--primary);
    }
    
    .detail-value {
        color: var(--text);
    }
    
    .total-price {
        font-weight: 600;
        font-size: 18px;
    }
    
    .section-subtitle {
        font-family: 'Orbitron', sans-serif;
        font-size: 18px;
        margin-bottom: 20px;
        color: var(--primary);
    }
    
    .order-items {
        margin-bottom: 30px;
    }
    
    .order-item {
        display: flex;
        gap: 20px;
        padding: 15px 0;
        border-bottom: 1px solid var(--border);
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .item-image {
        width: 80px;
        height: 80px;
        flex-shrink: 0;
    }
    
    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 4px;
    }
    
    .placeholder-image {
        width: 100%;
        height: 100%;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--light-text);
        border-radius: 4px;
    }
    
    .item-info {
        flex: 1;
    }
    
    .item-name {
        font-weight: 500;
        margin-bottom: 5px;
        color: var(--primary);
    }
    
    .item-meta {
        display: flex;
        gap: 15px;
        font-size: 14px;
    }
    
    .item-price, .item-subtotal {
        font-weight: 500;
    }
    
    .confirmation-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
    }
    
    .btn {
        padding: 12px 25px;
        border-radius: 4px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .continue-shopping-btn {
        background-color: var(--border);
        color: var(--text);
    }
    
    .continue-shopping-btn:hover {
        background-color: var(--dark-gray);
        color: white;
    }
    
    .view-orders-btn {
        background-color: var(--primary);
        color: white;
    }
    
    .view-orders-btn:hover {
        background-color: var(--primary-light);
    }
    
    @media (max-width: 768px) {
        .confirmation-card {
            padding: 20px;
        }
        
        .confirmation-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            text-align: center;
        }
        
        .item-meta {
            flex-wrap: wrap;
            gap: 8px;
        }
    }
</style>

<?php include 'footer.php'; ?>