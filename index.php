<?php
require_once 'includes/auth_functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniMarket - University Marketplace</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
    <link rel="icon" type="image/png" href="/assets/images/logo/tab-logo.png">
    <style>
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(46, 125, 50, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('/assets/images/hero/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: var(--light-text);
            padding: 120px 0;
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .hero .container {
            position: relative;
            z-index: 2;
            background: transparent;
            box-shadow: none;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            border: none;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .hero p {
            font-size: 1.3rem;
            max-width: 800px;
            margin: 0 auto 30px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
        
        .hero .btn {
            padding: 12px 30px;
            font-size: 1.1rem;
            margin: 0 10px;
            transition: all 0.3s ease;
            background-color: transparent;
            border: 2px solid var(--light-text);
            color: var(--light-text);
        }
        
        .hero .btn:hover {
            background-color: var(--light-text);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .hero .btn-secondary {
            background-color: transparent;
            border: 2px solid var(--light-text);
        }

        .hero .btn-secondary:hover {
            background-color: var(--light-text);
            color: var(--primary-color);
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }
        
        .feature-card {
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            margin-bottom: 15px;
            color: var(--primary-dark);
        }
        
        .cta-section {
            background-color: #f5f5f5;
            padding: 60px 0;
            text-align: center;
            margin-top: 40px;
        }
        
        .trending-products {
            margin: 60px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            color: var(--primary-dark);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="hero">
        <div class="container">
            <h1>Welcome to UniMarket</h1>
            <p>The University Marketplace for Affordable Student Essentials, Books, Supplies & Campus Merchandise.</p>
            <?php if (!isLoggedIn()): ?>
                <div>
                    <a href="/auth/register.php" class="btn" style="margin-right: 10px;">Join Now</a>
                    <a href="/auth/login.php" class="btn btn-secondary">Sign In</a>
                </div>
            <?php else: ?>
                <div>
                    <?php if (isCustomer()): ?>
                        <a href="/public/customer/products/browse.php" class="btn">Browse Products</a>
                    <?php else: ?>
                        <a href="/public/owner/dashboard.php" class="btn">Go to Dashboard</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">🛍️</div>
                <h3>Campus Marketplace</h3>
                <p>Buy and sell products exclusively within your university community.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔄</div>
                <h3>Easy Pickup</h3>
                <p>Schedule convenient on-campus pickup times for your orders.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🛒</div>
                <h3>Secure Transactions</h3>
                <p>Safe and reliable transactions between students and campus vendors.</p>
            </div>
        </div>
        
        <?php
        // Display trending products (only for customers or guests)
        if (!isLoggedIn() || isCustomer()) {
            require_once 'config/database.php';
            
            $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
            $trending_products = $stmt->fetchAll();
            
            if (!empty($trending_products)) {
                echo '<div class="trending-products">';
                echo '<h2 class="section-title">Trending Products</h2>';
                echo '<div class="product-grid">';
                
                foreach ($trending_products as $product) {
                    echo '<div class="product-card"">';
                    echo '<div class="product-image">';
                    if ($product['image_path']) {
                        echo '<img src="/uploads/products/'.$product['image_path'].'" alt="'.htmlspecialchars($product['name']).'">';
                    } else {
                        echo '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;">No Image</div>';
                    }
                    echo '</div>';
                    echo '<div class="product-info">';
                    echo '<h3 class="product-title">'.htmlspecialchars($product['name']).'</h3>';
                    echo '<p class="product-price">₱'.number_format($product['price'], 2).'</p>';
                    
                    if (isLoggedIn()) {
                        echo '<a href="/customer/products/view.php?id='.$product['product_id'].'" class="btn" style="display: block; text-align: center;">';
                        echo '<i class="fas fa-eye"></i> View Details';
                        echo '</a>';
                    } else {
                        echo '<a href="/auth/login.php" class="btn" style="display: block; text-align: center;">Login to Purchase</a>';
                    }
                    
                    echo '</div>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '<div style="text-align: center; margin-top: 30px;">';
                echo '<a href="/customer/products/browse.php" class="btn">View All Products</a>';
                echo '</div>';
                echo '</div>';
            }
        }
        ?>
    </div>
    
    <div class="cta-section">
        <div class="container">
            <h2>Ready to join the campus marketplace?</h2>
            <p style="max-width: 600px; margin: 0 auto 30px;">Whether you're looking to buy or sell, UniMarket connects you with your university community.</p>
            <?php if (!isLoggedIn()): ?>
                <a href="/auth/register.php" class="btn">Get Started</a>
            <?php else: ?>
                <?php if (isCustomer()): ?>
                    <a href="/customer/products/browse.php" class="btn">Browse Products</a>
                <?php else: ?>
                    <a href="/owner/products/add.php" class="btn">Add New Product</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
