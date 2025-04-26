<?php
// Database connection
$db = new mysqli('localhost', 'root', '', 'ecommerce_db');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get filter and search parameters
$category_filter = isset($_GET['category']) ? $db->real_escape_string($_GET['category']) : '';
$search_query = isset($_GET['search']) ? $db->real_escape_string($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $db->real_escape_string($_GET['sort']) : 'name';

// Validate sort parameter
$valid_sorts = ['name', 'price_asc', 'price_desc', 'rating'];
if (!in_array($sort_by, $valid_sorts)) {
    $sort_by = 'name';
}

// Build the SQL query
$query = "SELECT * FROM products WHERE 1=1";

if (!empty($category_filter)) {
    $query .= " AND category = '$category_filter'";
}

if (!empty($search_query)) {
    $query .= " AND (name LIKE '%$search_query%' OR description LIKE '%$search_query%')";
}

// Add sorting
switch ($sort_by) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'rating':
        $query .= " ORDER BY rating DESC";
        break;
    default:
        $query .= " ORDER BY name";
}

// Execute query
$result = $db->query($query);
$products = $result->fetch_all(MYSQLI_ASSOC);

// Get distinct categories for filter dropdown
$categories_result = $db->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL");
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// Current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products | UniMarket</title>
    <style>
        /* Reuse the same styles from your main CSS */
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
        
        /* Header Styles */
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
        
        /* Products Page Specific Styles */
        .products-hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://via.placeholder.com/1200x400');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
            margin-bottom: 40px;
            border-radius: 10px;
        }
        
        .products-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .products-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .search-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .search-box {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-width: 250px;
        }
        
        .category-filter, .sort-filter {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
        }
        
        .search-button {
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .results-count {
            color: #666;
            font-size: 14px;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        
        .product-card {
            background: white;
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .product-image {
            height: 200px;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-info h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .product-price {
            font-size: 20px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        
        .product-rating {
            color: #f39c12;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .product-rating .stars {
            margin-right: 5px;
        }
        
        .product-actions {
            display: flex;
            justify-content: space-between;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
            border: 1px solid #3498db;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-outline {
            background: white;
            color: #3498db;
            border: 1px solid #3498db;
        }
        
        .btn-outline:hover {
            background: #f8f9fa;
        }
        
        .no-results {
            text-align: center;
            padding: 50px;
            grid-column: 1 / -1;
        }
        
        .no-results-icon {
            font-size: 50px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-bottom: 60px;
        }
        
        .pagination a {
            padding: 8px 16px;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #3498db;
        }
        
        .pagination a.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .pagination a:hover:not(.active) {
            background: #f1f1f1;
        }
        
        /* Footer Styles */
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
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .products-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-filter {
                flex-direction: column;
                gap: 10px;
            }
            
            .products-hero h1 {
                font-size: 36px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">UniMarket</div>
                <ul style="user-select: none;" class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="products.php" class="active">Products</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Hero Section -->
            <section class="products-hero">
                <h1>Our Products</h1>
                <p>Discover our high-quality selection of products</p>
            </section>
            
            <!-- Products Section -->
            <section class="products-section">
                <div class="products-controls">
                    <form method="get" class="search-filter">
                        <input type="text" name="search" class="search-box" placeholder="Search products..." value="<?php echo htmlspecialchars($search_query); ?>">
                        
                        <select name="category" class="category-filter">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['category']); ?>" <?php echo $category_filter == $category['category'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['category']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select name="sort" class="sort-filter">
                            <option value="name" <?php echo $sort_by == 'name' ? 'selected' : ''; ?>>Sort by Name</option>
                            <option value="price_asc" <?php echo $sort_by == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_desc" <?php echo $sort_by == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="rating" <?php echo $sort_by == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                        </select>
                        
                        <button type="submit" class="search-button">Apply</button>
                    </form>
                    
                    <div class="results-count">
                        <?php echo count($products); ?> product(s) found
                    </div>
                </div>
                
                <div class="products-grid">
                    <?php if (empty($products)): ?>
                        <div class="no-results">
                            <div class="no-results-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3>No products found</h3>
                            <p>Try adjusting your search or filter criteria</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <a href="product.php?id=<?php echo $product['id']; ?>">
                                    <div class="product-image">
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </div>
                                    <div class="product-info">
                                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                                        <div class="product-rating">
                                            <span class="stars">
                                                <?php 
                                                $full_stars = floor($product['rating']);
                                                $half_star = ceil($product['rating'] - $full_stars);
                                                $empty_stars = 5 - $full_stars - $half_star;
                                                
                                                echo str_repeat('<i class="fas fa-star"></i>', $full_stars);
                                                echo str_repeat('<i class="fas fa-star-half-alt"></i>', $half_star);
                                                echo str_repeat('<i class="far fa-star"></i>', $empty_stars);
                                                ?>
                                            </span>
                                            <span>(<?php echo $product['rating']; ?>)</span>
                                        </div>
                                        <div class="product-actions">
                                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline">Details</a>
                                            <a href="#" class="btn btn-primary">Add to Cart</a>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination (example - would need backend implementation) -->
                <div class="pagination">
                    <a href="#">&laquo;</a>
                    <a href="#" class="active">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#">&raquo;</a>
                </div>
            </section>
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
            <p>&copy; <?php echo date('Y'); ?> ShopName. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>