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

$page_title = 'Technical Support - Bitronics';
?>

<?php include 'header.php'; ?>

<main class="section-container">
    <div class="page-container">
        <h1 class="page-title">Technical Support</h1>
        
        <div class="support-content">
            <div class="support-section">
                <h2><i class="fas fa-headset"></i> Contact Support</h2>
                <p>Our technical support team is available to help you with any issues or questions you may have.</p>
                <div class="contact-methods">
                    <p><strong>Email:</strong> support@bitronics.ph</p>
                    <p><strong>Phone:</strong> (02) 8123 4567</p>
                    <p><strong>Live Chat:</strong> Available 9AM-6PM, Monday to Saturday</p>
                </div>
            </div>
            
            <div class="support-section">
                <h2><i class="fas fa-question-circle"></i> FAQs</h2>
                <div class="faq-item">
                    <h3>How do I check my warranty status?</h3>
                    <p>You can check your warranty status by entering your product serial number on our warranty page.</p>
                </div>
                <div class="faq-item">
                    <h3>What is your return policy?</h3>
                    <p>We offer a 7-day return policy for defective items. Please contact our support team to initiate a return.</p>
                </div>
                <div class="faq-item">
                    <h3>How long does shipping take?</h3>
                    <p>Metro Manila orders typically arrive within 1-2 business days. Provincial orders take 3-5 business days.</p>
                </div>
            </div>
            
            <div class="support-section">
                <h2><i class="fas fa-download"></i> Drivers & Downloads</h2>
                <p>Find the latest drivers and software for your products:</p>
                <a href="#" class="download-btn">Motherboard Drivers</a>
                <a href="#" class="download-btn">Graphics Card Drivers</a>
                <a href="#" class="download-btn">Peripheral Software</a>
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
    
    .support-section {
        margin-bottom: 40px;
        padding: 25px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .support-section h2 {
        color: var(--primary);
        margin-bottom: 20px;
        font-size: 22px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .contact-methods {
        margin-top: 15px;
    }
    
    .contact-methods p {
        margin-bottom: 10px;
    }
    
    .faq-item {
        margin-bottom: 20px;
    }
    
    .faq-item h3 {
        color: var(--primary-light);
        margin-bottom: 8px;
        font-size: 18px;
    }
    
    .download-btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: var(--primary);
        color: white;
        border-radius: 4px;
        margin-right: 10px;
        margin-bottom: 10px;
        text-decoration: none;
        transition: background-color 0.3s;
    }
    
    .download-btn:hover {
        background-color: var(--primary-light);
    }
</style>

<?php include 'footer.php'; ?>