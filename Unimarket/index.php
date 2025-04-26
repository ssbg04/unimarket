<?php
// Database connection
$db = new mysqli('localhost', 'root', '', 'ecommerce_db');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get all products for the products page
function getProducts($filter = '', $search = '') {
    global $db;
    $query = "SELECT * FROM products";
    
    $conditions = [];
    if (!empty($filter)) {
        $conditions[] = "category = '$filter'";
    }
    if (!empty($search)) {
        $conditions[] = "(name LIKE '%$search%' OR description LIKE '%$search%')";
    }
    
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $result = $db->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get single product details
function getProduct($id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Get product reviews
function getReviews($product_id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM reviews WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['search'])) {
        $search = $_POST['search'];
        $filter = $_POST['filter'] ?? '';
        header("Location: products.php?search=$search&filter=$filter");
        exit();
    }
    
    if (isset($_POST['add_review'])) {
        $product_id = $_POST['product_id'];
        $name = $_POST['name'];
        $rating = $_POST['rating'];
        $comment = $_POST['comment'];
        
        $stmt = $db->prepare("INSERT INTO reviews (product_id, name, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $product_id, $name, $rating, $comment);
        $stmt->execute();
        
        header("Location: product.php?id=$product_id");
        exit();
    }
}

// Current page
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniMarket</title>
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background: #2c3e50;
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin-left: 20px;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #3498db;
        }
        
        .nav-links a.active {
            color: #3498db;
            border-bottom: 2px solid #3498db;
        }
        
        /* Main Content */
        main {
            min-height: calc(100vh - 150px);
            padding: 40px 0;
        }
        
        /* Home Page */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://plsp.edu.ph/wp-content/uploads/2022/12/SL1-1024x396.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
            border-radius: 10px;
            margin-bottom: 40px;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .featured-products {
            margin-top: 40px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 30px;
        }
        
        /* Products Page */
        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .search-filter {
            display: flex;
            gap: 10px;
        }
        
        .search-filter input, .search-filter select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .search-filter button {
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .product-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .product-image {
            height: 200px;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-info h3 {
            margin-bottom: 10px;
        }
        
        .product-price {
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        
        .product-rating {
            color: #f39c12;
            margin-bottom: 15px;
        }
        
        /* Product Detail Page */
        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        
        .product-gallery img {
            width: 100%;
            border-radius: 10px;
        }
        
        .product-title {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .product-price {
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .product-rating {
            margin-bottom: 20px;
        }
        
        .product-description {
            margin-bottom: 30px;
            line-height: 1.8;
        }
        
        .specs-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .specs-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .specs-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .specs-table td:first-child {
            font-weight: bold;
            width: 30%;
        }
        
        .reviews {
            margin-top: 40px;
        }
        
        .review {
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
        }
        
        .review:last-child {
            border-bottom: none;
        }
        
        .review-author {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .review-rating {
            color: #f39c12;
            margin-bottom: 10px;
        }
        
        .review-form {
            margin-top: 40px;
            background: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .form-group textarea {
            min-height: 100px;
        }
        
        /* Footer */
        footer {
            background: #2c3e50;
            color: white;
            padding: 30px 0;
            text-align: center;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            list-style: none;
            margin-bottom: 20px;
        }
        
        .footer-links li {
            margin: 0 15px;
        }
        
        .footer-links a {
            color: white;
            text-decoration: none;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
            }
            
            .products-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">UniMarket</div>
                <ul style="user-select: none;" class="nav-links">
                    <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Home</a></li>
                    <li><a href="about.php" class="<?= $current_page == 'about.php' ? 'active' : '' ?>">About</a></li>
                    <li><a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>">Products</a></li>
                    <li><a href="contact.php" class="<?= $current_page == 'contact.php' ? 'active' : '' ?>">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <?php
            // Home Page
            if ($current_page == 'index.php') {
                echo '
                <section class="hero">
                    <h1>Welcome to Our Shop</h1>
                    <p>Discover school/office items at University E-Shop</p>
                    <a href="products.php" class="btn">Shop Now</a>
                </section>
                
                <section class="featured-products">
                    <div class="section-title">
                        <h2>Featured Products</h2>
                    </div>
                    <div class="products-grid">
                        <!-- Sample featured products -->
                        <div class="product-card">
                            <a href="product.php?id=1">
                                <div class="product-image">
                                    <img src="https://via.placeholder.com/300x300" alt="Product 1">
                                </div>
                                <div class="product-info">
                                    <h3>Notebook</h3>
                                    <div class="product-price">₱50</div>
                                    <div class="product-rating">★★★★☆ (4.2)</div>
                                    <a href="product.php?id=1" class="btn">View Details</a>
                                </div>
                            </a>
                        </div>
                        
                        <div class="product-card">
                            <a href="product.php?id=2">
                                <div class="product-image">
                                    <img src="https://via.placeholder.com/300x300" alt="Product 2">
                                </div>
                                <div class="product-info">
                                    <h3>1-Box of Ballpen</h3>
                                    <div class="product-price">₱80</div>
                                    <div class="product-rating">★★★★★ (4.8)</div>
                                    <a href="product.php?id=2" class="btn">View Details</a>
                                </div>
                            </a>
                        </div>
                        
                        <div class="product-card">
                            <a href="product.php?id=3">
                                <div class="product-image">
                                    <img src="https://via.placeholder.com/300x300" alt="Product 3">
                                </div>
                                <div class="product-info">
                                    <h3>White Shirt</h3>
                                    <div class="product-price">₱300</div>
                                    <div class="product-rating">★★★★☆ (4.3)</div>
                                    <a href="product.php?id=3" class="btn">View Details</a>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>';
            }
            
            // About Page
            elseif ($current_page == 'about.php') {
                echo '
                <section class="about">
                    <div class="section-title">
                        <h2>About Us</h2>
                    </div>
                    <div class="about-content">
                        <p>Welcome to ShopName, your number one source for all things [product]. We\'re dedicated to providing you the very best of [product], with an emphasis on quality, customer service, and uniqueness.</p>
                        
                        <p>Founded in [year] by [founder\'s name], ShopName has come a long way from its beginnings in [starting location]. When [founder\'s name] first started out, [his/her/their] passion for [brand message - e.g., "eco-friendly cleaning products"] drove them to [action: quit day job, do tons of research, etc.] so that ShopName can offer you [competitive differentiator - e.g., "the world\'s most advanced toothbrush"]. We now serve customers all over [place - town, country, the world], and are thrilled that we\'re able to turn our passion into [my/our] own website.</p>
                        
                        <p>We hope you enjoy our products as much as we enjoy offering them to you. If you have any questions or comments, please don\'t hesitate to contact us.</p>
                        
                        <p>Sincerely,<br>The ShopName Team</p>
                    </div>
                </section>';
            }
            
            // Products Page
            elseif ($current_page == 'products.php') {
                $filter = $_GET['filter'] ?? '';
                $search = $_GET['search'] ?? '';
                $products = getProducts($filter, $search);
                
                echo '
                <section class="products">
                    <div class="products-header">
                        <div class="section-title">
                            <h2>Our Products</h2>
                        </div>
                        <form method="post" class="search-filter">
                            <input type="text" name="search" placeholder="Search products..." value="'.htmlspecialchars($search).'">
                            <select name="filter">
                                <option value="">All Categories</option>
                                <option value="Electronics" '.($filter == 'Electronics' ? 'selected' : '').'>Electronics</option>
                                <option value="Clothing" '.($filter == 'Clothing' ? 'selected' : '').'>Clothing</option>
                                <option value="Home" '.($filter == 'Home' ? 'selected' : '').'>Home</option>
                                <option value="Sports" '.($filter == 'Sports' ? 'selected' : '').'>Sports</option>
                            </select>
                            <button type="submit">Search</button>
                        </form>
                    </div>
                    
                    <div class="products-grid">';
                
                if (empty($products)) {
                    echo '<p>No products found matching your criteria.</p>';
                } else {
                    foreach ($products as $product) {
                        echo '
                        <div class="product-card">
                            <a href="product.php?id='.$product['id'].'">
                                <div class="product-image">
                                    <img src="'.$product['image'].'" alt="'.$product['name'].'">
                                </div>
                                <div class="product-info">
                                    <h3>'.$product['name'].'</h3>
                                    <div class="product-price">$'.number_format($product['price'], 2).'</div>
                                    <div class="product-rating">'.str_repeat('★', floor($product['rating'])).str_repeat('☆', 5 - floor($product['rating'])).' ('.$product['rating'].')</div>
                                    <a href="product.php?id='.$product['id'].'" class="btn">View Details</a>
                                </div>
                            </a>
                        </div>';
                    }
                }
                
                echo '
                    </div>
                </section>';
            }
            
            // Product Detail Page
            elseif ($current_page == 'product.php' && isset($_GET['id'])) {
                $product = getProduct($_GET['id']);
                $reviews = getReviews($_GET['id']);
                
                if ($product) {
                    echo '
                    <section class="product-detail">
                        <div class="product-gallery">
                            <img src="'.$product['image'].'" alt="'.$product['name'].'">
                        </div>
                        
                        <div class="product-info">
                            <h1 class="product-title">'.$product['name'].'</h1>
                            <div class="product-price">$'.number_format($product['price'], 2).'</div>
                            <div class="product-rating">'.str_repeat('★', floor($product['rating'])).str_repeat('☆', 5 - floor($product['rating'])).' ('.$product['rating'].')</div>
                            
                            <p class="product-description">'.$product['description'].'</p>
                            
                            <table class="specs-table">
                                <tr>
                                    <td>Category</td>
                                    <td>'.$product['category'].'</td>
                                </tr>
                                <tr>
                                    <td>Brand</td>
                                    <td>'.$product['brand'].'</td>
                                </tr>
                                <tr>
                                    <td>Stock</td>
                                    <td>'.($product['stock'] > 0 ? 'In Stock' : 'Out of Stock').'</td>
                                </tr>
                                <!-- Add more specifications as needed -->
                            </table>
                            
                            <a href="#" class="btn" style="display: inline-block; margin-right: 10px;">Buy Now</a>
                            <a href="#" class="btn" style="display: inline-block; background: #e74c3c;">Add to Cart</a>
                        </div>
                    </section>
                    
                    <section class="reviews">
                        <h2>Customer Reviews</h2>';
                    
                    if (empty($reviews)) {
                        echo '<p>No reviews yet. Be the first to review!</p>';
                    } else {
                        foreach ($reviews as $review) {
                            echo '
                            <div class="review">
                                <div class="review-author">'.$review['name'].'</div>
                                <div class="review-rating">'.str_repeat('★', $review['rating']).str_repeat('☆', 5 - $review['rating']).'</div>
                                <div class="review-comment">'.$review['comment'].'</div>
                            </div>';
                        }
                    }
                    
                    echo '
                        <div class="review-form">
                            <h3>Write a Review</h3>
                            <form method="post">
                                <input type="hidden" name="product_id" value="'.$_GET['id'].'">
                                <input type="hidden" name="add_review" value="1">
                                
                                <div class="form-group">
                                    <label for="name">Your Name</label>
                                    <input type="text" id="name" name="name" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="rating">Rating</label>
                                    <select id="rating" name="rating" required>
                                        <option value="">Select Rating</option>
                                        <option value="1">1 Star</option>
                                        <option value="2">2 Stars</option>
                                        <option value="3">3 Stars</option>
                                        <option value="4">4 Stars</option>
                                        <option value="5">5 Stars</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="comment">Your Review</label>
                                    <textarea id="comment" name="comment" required></textarea>
                                </div>
                                
                                <button type="submit" class="btn">Submit Review</button>
                            </form>
                        </div>
                    </section>';
                } else {
                    echo '<p>Product not found.</p>';
                }
            }
            
            // Contact Page
            elseif ($current_page == 'contact.php') {
                echo '
                <section class="contact">
                    <div class="section-title">
                        <h2>Contact Us</h2>
                    </div>
                    
                    <div class="contact-content">
                        <div class="contact-info">
                            <h3>Get In Touch</h3>
                            <p>Have questions or feedback? We\'d love to hear from you!</p>
                            
                            <div class="contact-details">
                                <div>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>123 Shop Street, City, Country</span>
                                </div>
                                <div>
                                    <i class="fas fa-phone"></i>
                                    <span>+1 (123) 456-7890</span>
                                </div>
                                <div>
                                    <i class="fas fa-envelope"></i>
                                    <span>info@shopname.com</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="contact-form">
                            <form method="post">
                                <div class="form-group">
                                    <label for="name">Your Name</label>
                                    <input type="text" id="name" name="name" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Your Email</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="subject">Subject</label>
                                    <input type="text" id="subject" name="subject" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="message">Message</label>
                                    <textarea id="message" name="message" required></textarea>
                                </div>
                                
                                <button type="submit" class="btn">Send Message</button>
                            </form>
                        </div>
                    </div>
                </section>';
            }
            ?>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <p>&copy; <?php echo date('Y'); ?> UniMarket. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>