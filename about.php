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

$page_title = 'About Us - Bitronics';
?>

<?php include 'header.php'; ?>

<main class="section-container">
    <div class="page-container">
        <h1 class="page-title">About Bitronics</h1>
        
        <div class="page-content">
            <div class="about-section">
                <h2>Our Story</h2>
                <p>Founded in 2010, Bitronics has grown from a small computer parts retailer to one of the leading tech suppliers in the Philippines. Our passion for technology and commitment to customer satisfaction has driven our success over the years.</p>
            </div>
            
            <div class="about-section">
                <h2>Our Mission</h2>
                <p>To provide high-quality computer components and electronics at competitive prices, backed by exceptional customer service and technical expertise.</p>
            </div>
            
            <div class="about-section">
                <h2>Our Team</h2>
                <p>Our team consists of tech enthusiasts and certified professionals who are always ready to help you find the perfect components for your needs.</p>
            </div>
            
            <div class="about-section">
                <h2>Why Choose Us?</h2>
                <ul>
                    <li>Genuine products with warranty</li>
                    <li>Competitive pricing</li>
                    <li>Fast and reliable shipping</li>
                    <li>Knowledgeable support team</li>
                    <li>Secure payment options</li>
                </ul>
            </div>
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
    
    .page-content {
        line-height: 1.6;
    }
    
    .about-section {
        margin-bottom: 40px;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .about-section h2 {
        color: var(--primary);
        margin-bottom: 15px;
        font-size: 22px;
    }
    
    .about-section ul {
        padding-left: 20px;
    }
    
    .about-section ul li {
        margin-bottom: 10px;
    }
</style>

<?php include 'footer.php'; ?>