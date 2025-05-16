<?php
require_once 'config.php';
session_start();

// Get search query if any
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (product_name LIKE ? OR product_description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

$query .= " ORDER BY created_at DESC";

// Get products
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get cart count for header
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = $stmt->fetch()['count'];
}

$page_title = 'Products - Bitronics';
?>

<?php include 'header.php'; ?>

<main class="section-container">
    <h1 class="section-title">Our Products</h1>
    
    <?php if (!empty($search) || !empty($category)): ?>
        <div class="search-results-header">
            <h2>Search Results</h2>
            <p class="search-results-count">
                <?php echo count($products); ?> product(s) found
                <?php if (!empty($search)): ?>
                    for "<?php echo htmlspecialchars($search); ?>"
                <?php endif; ?>
                <?php if (!empty($category)): ?>
                    in category "<?php echo htmlspecialchars($category); ?>"
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
    
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-card-wrapper">
                    <?php if ($product['is_featured']): ?>
                        <span class="featured-badge">Featured</span>
                    <?php endif; ?>
                    
                    <img src="<?php echo htmlspecialchars($product['product_image'] ?? 'https://via.placeholder.com/300'); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                         class="product-image">
                </div>
                
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                    <div class="product-sku">SKU: <?php echo htmlspecialchars($product['sku']); ?></div>
                    
                    <div class="product-actions">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="view-btn">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        
                        <form action="cart.php" method="post" style="flex: 1;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" name="add_to_cart" class="add-to-cart-btn">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<style>
    .section-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .section-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 32px;
        margin-bottom: 30px;
        color: var(--primary);
        text-align: center;
        position: relative;
        padding-bottom: 15px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 3px;
        background: var(--primary);
    }
    
    .search-results-header {
        margin-bottom: 30px;
        text-align: center;
    }
    
    .search-results-count {
        color: var(--light-text);
        font-size: 16px;
        margin-top: 10px;
    }
    
    .products-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 25px;
        margin-top: 30px;
        justify-content: flex-start;
    }
    
    .product-card {
        flex: 0 0 calc(33.333% - 25px); /* 3 columns with gap */
        max-width: calc(33.333% - 25px);
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: auto;
        margin-bottom: 25px;
    }

    
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }
    
    .product-card-wrapper {

        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .product-image-container {
        
        padding: 25px;
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        text-align: center;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .product-image {

        max-width: 100%;
        max-height: 200px;
        object-fit: contain;
        transition: transform 0.3s;
    }
    
    .product-card:hover .product-image {
        transform: scale(1.05);
    }
    
    .product-info {
        padding: 20px;
        border-top: 1px solid rgba(0,0,0,0.05);
    }
    
    .product-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 18px;
        margin-bottom: 12px;
        color: var(--primary);
        height: 50px;
        overflow: hidden;
        line-height: 1.3;
    }
    
    .product-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    
    .product-price {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary);
    }
    
    .product-stock {
        font-size: 13px;
        padding: 3px 8px;
        border-radius: 4px;
        background-color: <?php echo ($product['stock_quantity'] > 0) ? 'var(--success-light)' : 'var(--danger-light)'; ?>;
        color: <?php echo ($product['stock_quantity'] > 0) ? 'var(--success)' : 'var(--danger)'; ?>;
    }
    
    .product-sku {
        color: var(--light-text);
        margin-bottom: 15px;
        font-size: 13px;
    }
    
    .product-actions {
        display: flex;
        gap: 10px;
    }
    
    .view-btn, .add-to-cart-btn {
        padding: 12px;
        text-align: center;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .view-btn {
        background-color: var(--light-gray);
        color: var(--text);
        flex: 1;
    }
    
    .view-btn:hover {
        background-color: var(--dark-gray);
        color: white;
    }
    
    .add-to-cart-btn {
        background-color: var(--primary);
        color: white;
        border: none;
        cursor: pointer;
        flex: 1;
    }
    
    .add-to-cart-btn:hover {
        background-color: var(--primary-dark);
    }
    
    .featured-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: var(--success);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        z-index: 1;
    }
    
    .product-rating {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        color: #FFB800;
        font-size: 14px;
    }
    
    @media (max-width: 1024px) {
        .product-card {
            flex: 0 0 calc(50% - 25px); /* 2 columns on tablets */
            max-width: calc(50% - 25px);
        }
    }

    @media (max-width: 768px) {
        .products-grid {
            gap: 20px;
        }
        
        .product-card {
            flex: 0 0 100%; /* 1 column on mobile */
            max-width: 100%;
        }
        
        .product-actions {
            flex-direction: row; /* Keep buttons side by side */
        }
    }
</style>

<?php include 'footer.php'; ?>

