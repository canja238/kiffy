<?php
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    $cart_count = $result['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Bitronics'; ?></title>
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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--gray);
            color: var(--text);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .font-orbitron {
            font-family: 'Orbitron', sans-serif;
        }
        
        header {
            background-color: var(--white);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        
        .header-container {
            height: 50px;
            max-width: 100%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
        }
        
        .search-form {
            display: flex;
            align-items: center;
            border: 1px solid var(--border);
            border-radius: 0.25rem;
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            margin: 0 16px;
        }
        
        .search-input {
            padding: 8px 12px;
            font-size: 14px;
            color: var(--text);
            background-color: transparent;
            border: none;
            outline: none;
            flex-grow: 1;
        }
        
        .search-button {
            background-color: var(--primary);
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }
        
        .search-button:hover {
            background-color: var(--primary-light);
        }
        
        .search-button i {
            color: var(--white);
            font-size: 14px;
        }
        
        .cart-button {
            position: relative;
            background-color: var(--primary);
            padding: 8px;
            border-radius: 0.25rem;
            color: var(--white);
            border: none;
            cursor: pointer;
        }
        
        .cart-button:hover {
            background-color: var(--primary-light);
        }
        
        .cart-count {
            position: absolute;
            top: -4px;
            right: -4px;
            background-color: var(--white);
            color: var(--primary);
            font-size: 12px;
            font-weight: bold;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .account-dropdown {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 14px;
            font-weight: 600;
            color: var(--primary);
            cursor: pointer;
            user-select: none;
            position: relative;
        }
        
        .account-dropdown i.fa-chevron-down {
            font-size: 12px;
            transition: transform 0.2s;
        }
        
        .account-dropdown:hover i.fa-chevron-down {
            transform: rotate(180deg);
        }
        
        .user-button {
            background: none;
            border: none;
            color: var(--primary);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .user-button:hover {
            color: var(--primary-light);
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: var(--white);
            border-radius: 0.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            min-width: 160px;
            z-index: 100;
            padding: 8px 0;
            display: none;
        }
        
        .dropdown-menu a {
            display: block;
            padding: 8px 16px;
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .dropdown-menu a:hover {
            background-color: var(--gray);
            color: var(--primary-light);
        }
        
        .account-dropdown:hover .dropdown-menu {
            display: block;
        }
        
        nav {
            background-color: var(--primary);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }
        
        .nav-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            color: var(--white);
            font-size: 14px;
            padding: 8px 0;
            font-weight: 600;
            list-style: none;
            margin: 0;
        }
        
        .nav-list a {
            color: var(--white);
            text-decoration: none;
            padding: 4px 0;
            display: block;
        }
        
        .nav-list a:hover {
            text-decoration: underline;
        }
        
        main {
            flex-grow: 1;
        }
        
        .section-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 16px;
        }
        
        .section-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 20px;
            color: var(--primary);
            margin-bottom: 16px;
        }
        
        @media (max-width: 639px) {
            .header-container {
                flex-wrap: wrap;
                height: auto;
                padding: 8px;
            }
            
            .search-form {
                order: 3;
                width: 100%;
                margin: 8px 0;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1 class="font-orbitron">BITRONICS</h1>

            <form class="search-form" action="products.php" method="get">
                <input class="search-input" 
                       placeholder="Search for Products" 
                       type="text"
                       name="search"
                       aria-label="Search products"/>
                <button class="search-button" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            <div style="display: flex; align-items: center; gap: 16px;">
                <button class="cart-button" aria-label="Cart" onclick="window.location.href='cart.php'">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                </button>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="account-dropdown">
                        <button class="user-button">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="account.php"><i class="fas fa-user-circle"></i> My Account</a>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <a href="admin/"><i class="fas fa-cog"></i> Admin Panel</a>
                            <?php endif; ?>
                            <a href="orders.php"><i class="fas fa-clipboard-list"></i> My Orders</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" style="text-decoration: none; color: var(--primary); font-weight: 600;">LOGIN</a>
                    <span style="color: var(--primary);">|</span>
                    <a href="signup.php" style="text-decoration: none; color: var(--primary); font-weight: 600;">SIGN UP</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Navigation bar -->
        <nav>
            <div class="nav-container">
                <ul class="nav-list">
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="products.php">PRODUCTS</a></li>
                    <li><a href="brands.php">BRANDS</a></li>
                    <li><a href="support.php">TECHNICAL SUPPORT</a></li>
                    <li><a href="about.php">ABOUT US</a></li>
                </ul>
            </div>
        </nav>
    </header>