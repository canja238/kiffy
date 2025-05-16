<?php
require_once 'config.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header('Location: orders.php');
    exit;
}

$order_id = $_GET['id'];

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Get order items
$stmt = $pdo->prepare("SELECT oi.*, p.product_name, p.product_image, p.sku 
                       FROM order_items oi JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

// Get cart count for header
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = $stmt->fetch()['count'];
}

$page_title = 'Order Details - Bitronics';
?>

<?php include 'header.php'; ?>

<main class="section-container">
    <div class="order-details-container">
        <div class="order-header">
            <h1 class="section-title">Order #<?php echo $order['id']; ?></h1>
            <div class="order-status">
                <span class="status-badge <?php echo strtolower($order['status']); ?>">
                    <?php echo $order['status']; ?>
                </span>
            </div>
        </div>
        
        <div class="order-meta">
            <div class="meta-card">
                <h2 class="meta-title">Order Information</h2>
                <div class="meta-row">
                    <span class="meta-label">Order Date:</span>
                    <span class="meta-value"><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Order Total:</span>
                    <span class="meta-value">₱<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Payment Method:</span>
                    <span class="meta-value">Cash on Delivery</span>
                </div>
            </div>
        </div>
        
        <div class="order-items-section">
            <h2 class="section-subtitle">Order Items</h2>
            <div class="order-items-list">
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
                        <div class="item-details">
                            <h3 class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                            <div class="item-sku">SKU: <?php echo htmlspecialchars($item['sku']); ?></div>
                            <div class="item-price">₱<?php echo number_format($item['price'], 2); ?></div>
                        </div>
                        <div class="item-quantity">×<?php echo $item['quantity']; ?></div>
                        <div class="item-subtotal">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="order-summary">
            <h2 class="section-subtitle">Order Summary</h2>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>₱<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span>Free</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>₱<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>
        
        <div class="order-actions">
            <a href="orders.php" class="btn outline-btn">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
            <?php if ($order['status'] === 'Processing'): ?>
                <button class="btn danger-btn">
                    <i class="fas fa-times"></i> Cancel Order
                </button>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
    .order-details-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 25px;
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border);
    }
    
    .status-badge {
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 500;
    }
    
    .status-badge.completed {
        background-color: var(--success-light);
        color: var(--success);
    }
    
    .status-badge.processing {
        background-color: var(--warning-light);
        color: var(--warning);
    }
    
    .status-badge.shipped {
        background-color: var(--info-light);
        color: var(--info);
    }
    
    .status-badge.cancelled {
        background-color: var(--danger-light);
        color: var(--danger);
    }
    
    .order-meta {
        margin-bottom: 30px;
    }
    
    .meta-card {
        background-color: rgba(15, 47, 77, 0.05);
        border-radius: 8px;
        padding: 20px;
    }
    
    .meta-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 18px;
        margin-bottom: 15px;
        color: var(--primary);
    }
    
    .meta-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .meta-row:last-child {
        margin-bottom: 0;
    }
    
    .meta-label {
        font-weight: 500;
        color: var(--primary);
    }
    
    .meta-value {
        color: var(--text);
    }
    
    .section-subtitle {
        font-family: 'Orbitron', sans-serif;
        font-size: 18px;
        margin-bottom: 15px;
        color: var(--primary);
    }
    
    .order-items-list {
        margin-bottom: 30px;
    }
    
    .order-item {
        display: flex;
        align-items: center;
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
    
    .item-details {
        flex: 1;
    }
    
    .item-name {
        font-weight: 500;
        margin-bottom: 5px;
        color: var(--primary);
    }
    
    .item-sku {
        font-size: 14px;
        color: var(--light-text);
        margin-bottom: 5px;
    }
    
    .item-price {
        font-weight: 500;
    }
    
    .item-quantity {
        width: 60px;
        text-align: center;
    }
    
    .item-subtotal {
        width: 100px;
        text-align: right;
        font-weight: 500;
    }
    
    .order-summary {
        background-color: rgba(15, 47, 77, 0.05);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .summary-row.total {
        font-weight: 600;
        font-size: 18px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border);
    }
    
    .order-actions {
        display: flex;
        gap: 15px;
    }
    
    .btn {
        padding: 12px 25px;
        border-radius: 4px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .outline-btn {
        border: 1px solid var(--primary);
        color: var(--primary);
        background-color: transparent;
    }
    
    .outline-btn:hover {
        background-color: var(--primary);
        color: white;
    }
    
    .danger-btn {
        background-color: var(--danger);
        color: white;
        border: none;
        cursor: pointer;
    }
    
    .danger-btn:hover {
        background-color: var(--danger-dark);
    }
    
    @media (max-width: 768px) {
        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .order-item {
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .item-details {
            order: 1;
            flex: 100%;
        }
        
        .item-quantity, .item-subtotal {
            text-align: left;
        }
        
        .order-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            text-align: center;
        }
    }
</style>

<?php include 'footer.php'; ?>