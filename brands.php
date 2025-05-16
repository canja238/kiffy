<?php
require_once 'config.php';
session_start();

// Get cart count for header
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = $stmt->fetch()['count'];
}

$page_title = 'Brands - Bitronics';
?>

<?php include 'header.php'; ?>

<main class="section-container">
    <div class="page-container">
        <h1 class="page-title">Our Brands</h1>
        
        <div class="brands-grid">
            <?php
            // Option 1: If you have a different column name for brands (like manufacturer)
            // $stmt = $pdo->query("SELECT DISTINCT manufacturer FROM products WHERE manufacturer IS NOT NULL");
            
            // Option 2: If you don't have brands in your database, use a static list
            $brands = [
                ['name' => 'Asus'],
                ['name' => 'MSI'],
                ['name' => 'Gigabyte'],
                ['name' => 'Intel'],
                ['name' => 'AMD'],
                ['name' => 'Nvidia'],
                ['name' => 'Corsair'],
                ['name' => 'Samsung'],
                ['name' => 'Western Digital'],
                ['name' => 'Logitech']
            ];
            
            foreach ($brands as $brand) {
                echo '<div class="brand-card">';
                echo '<div class="brand-logo">';
                echo '<img src="images/brands/' . htmlspecialchars(strtolower(str_replace(' ', '-', $brand['name']))) . '.png" alt="' . htmlspecialchars($brand['name']) . '">';
                echo '</div>';
                echo '<h3>' . htmlspecialchars($brand['name']) . '</h3>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</main>

<style>
    .page-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .page-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 32px;
        margin-bottom: 30px;
        color: var(--primary);
        text-align: center;
    }
    
    .brands-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 30px;
        margin-top: 20px;
    }
    
    .brand-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }
    
    .brand-card:hover {
        transform: translateY(-5px);
    }
    
    .brand-logo {
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    
    .brand-logo img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    
    .brand-card h3 {
        margin-top: 10px;
        color: var(--primary);
    }
</style>

<?php include 'footer.php'; ?>