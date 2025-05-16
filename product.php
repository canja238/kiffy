<?php
require_once 'config.php';
session_start();

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$product_id = $_GET['id'];

// Get product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

// Get cart count for header
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = $stmt->fetch()['count'];
}

$page_title = htmlspecialchars($product['product_name']) . ' - Bitronics';
?>

<?php include 'header.php'; ?>

<main class="section-container">
    <div class="product-container">
        <div class="product-gallery">
            <img src="<?php echo htmlspecialchars($product['product_image'] ?? 'https://via.placeholder.com/500'); ?>" 
                 alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                 class="main-image">
        </div>
        
        <div class="product-details">
            <h1 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h1>
            <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
            <div class="product-sku">SKU: <?php echo htmlspecialchars($product['sku']); ?></div>
            
            <div class="product-description">
                <?php echo nl2br(htmlspecialchars($product['product_description'])); ?>
            </div>
            
            <form action="cart.php" method="post" class="add-to-cart-form">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                <button type="submit" name="add_to_cart" class="add-to-cart-btn">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </form>
            
            <div class="product-meta">
                <div class="meta-item">
                    <span class="meta-label">Category:</span>
                    <span><?php echo htmlspecialchars($product['category']); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Availability:</span>
                    <span><?php echo $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?></span>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .product-container {
        display: flex;
        gap: 40px;
        margin-top: 30px;
    }
    
    .product-gallery {
        flex: 1;
    }
    
    .main-image {
        width: 100%;
        max-height: 500px;
        object-fit: contain;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .product-details {
        flex: 1;
    }
    
    .product-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 24px;
        margin-bottom: 10px;
        color: var(--primary);
    }
    
    .product-price {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 20px;
        color: var(--primary);
    }
    
    .product-sku {
        color: var(--light-text);
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .product-description {
        margin-bottom: 30px;
        line-height: 1.6;
    }
    
    .add-to-cart-form {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .quantity-input {
        width: 80px;
        padding: 10px;
        text-align: center;
        border: 1px solid var(--border);
        border-radius: 4px;
    }
    
    .add-to-cart-btn {
        padding: 12px 30px;
        background-color: var(--primary);
        color: white;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .add-to-cart-btn:hover {
        background-color: var(--primary-light);
    }
    
    .product-meta {
        margin-top: 30px;
        border-top: 1px solid var(--border);
        padding-top: 20px;
    }
    
    .meta-item {
        margin-bottom: 10px;
    }
    
    .meta-label {
        font-weight: 500;
        color: var(--primary);
    }
    
    @media (max-width: 768px) {
        .product-container {
            flex-direction: column;
        }
    }
</style>

<?php include 'footer.php'; ?>