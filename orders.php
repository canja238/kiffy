<?php
require_once 'config.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get all orders for the current user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

// Get cart count for header
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = $stmt->fetch()['count'];
}

$page_title = 'My Orders - Bitronics';
?>

<?php include 'header.php'; ?>

<main class="section-container">
    <h1 class="section-title">My Orders</h1>
    
    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open empty-icon"></i>
            <h2>No Orders Found</h2>
            <p>You haven't placed any orders yet.</p>
            <a href="products.php" class="btn primary-btn">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <?php
                // Get order items for this order
                $stmt = $pdo->prepare("SELECT oi.*, p.product_name, p.product_image 
                                      FROM order_items oi JOIN products p ON oi.product_id = p.id 
                                      WHERE oi.order_id = ?");
                $stmt->execute([$order['id']]);
                $items = $stmt->fetchAll();
                ?>
                
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-meta">
                            <span class="order-number">Order #<?php echo $order['id']; ?></span>
                            <span class="order-date"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="order-status">
                            <span class="status-badge <?php echo strtolower($order['status']); ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="order-items-preview">
                        <?php foreach (array_slice($items, 0, 3) as $item): ?>
                            <div class="preview-item">
                                <?php if ($item['product_image']): ?>
                                    <img src="<?php echo htmlspecialchars($item['product_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                         class="preview-image">
                                <?php else: ?>
                                    <div class="preview-image placeholder">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="preview-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                                <span class="preview-quantity">×<?php echo $item['quantity']; ?></span>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($items) > 3): ?>
                            <div class="preview-more">
                                +<?php echo count($items) - 3; ?> more items
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="order-footer">
                        <div class="order-total">
                            <span>Total:</span>
                            <span class="total-amount">₱<?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                        <div class="order-actions">
                            <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn outline-btn">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<style>
    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-top: 20px;
    }
    
    .order-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s;
    }
    
    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background-color: rgba(15, 47, 77, 0.05);
        border-bottom: 1px solid var(--border);
    }
    
    .order-meta {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .order-number {
        font-weight: 600;
        color: var(--primary);
    }
    
    .order-date {
        font-size: 14px;
        color: var(--light-text);
    }
    
    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 14px;
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
    
    .order-items-preview {
        padding: 15px 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
    }
    
    .preview-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .preview-image {
        width: 40px;
        height: 40px;
        object-fit: contain;
        border-radius: 4px;
    }
    
    .preview-image.placeholder {
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--light-text);
    }
    
    .preview-name {
        font-size: 14px;
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .preview-quantity {
        font-size: 14px;
        color: var(--light-text);
    }
    
    .preview-more {
        font-size: 14px;
        color: var(--light-text);
        padding: 5px 10px;
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 4px;
    }
    
    .order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-top: 1px solid var(--border);
    }
    
    .order-total {
        font-weight: 500;
    }
    
    .total-amount {
        font-weight: 600;
        color: var(--primary);
        margin-left: 5px;
    }
    
    .order-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
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
    
    .primary-btn {
        background-color: var(--primary);
        color: white;
    }
    
    .primary-btn:hover {
        background-color: var(--primary-light);
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .empty-icon {
        font-size: 60px;
        color: var(--light-text);
        margin-bottom: 20px;
    }
    
    .empty-state h2 {
        font-family: 'Orbitron', sans-serif;
        color: var(--primary);
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: var(--light-text);
        margin-bottom: 20px;
    }
    
    @media (max-width: 768px) {
        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .order-footer {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .order-actions {
            width: 100%;
        }
        
        .btn {
            width: 100%;
            text-align: center;
        }
    }
</style>

<?php include 'footer.php'; ?>