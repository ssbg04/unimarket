<?php
// Database connection (same as in other pages)
$db = new mysqli('localhost', 'root', '', 'ecommerce_db');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | UniMarket</title>
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
        
        /* About Page Specific Styles */
        .about-hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://hips.hearstapps.com/hmg-prod/images/exterior-view-of-gimbels-department-store-news-photo-530837312-1542315991.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
            margin-bottom: 40px;
            border-radius: 10px;
        }
        
        .about-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .section-title h2 {
            font-size: 36px;
            color: #2c3e50;
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: #3498db;
        }
        
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
        }
        
        .about-text h3 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .about-text p {
            margin-bottom: 20px;
            line-height: 1.8;
        }
        
        .about-image img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .mission-vision {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 60px;
        }
        
        .mission, .vision {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .mission h3, .vision h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #2c3e50;
            text-align: center;
        }
        
        .team {
            margin-bottom: 60px;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .team-member {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .team-member:hover {
            transform: translateY(-10px);
        }
        
        .team-member img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .team-info {
            padding: 20px;
        }
        
        .team-info h4 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .team-info p {
            color: #3498db;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .social-links a {
            color: #2c3e50;
            font-size: 18px;
            transition: color 0.3s;
        }
        
        .social-links a:hover {
            color: #3498db;
        }
        
        /* Values Section */
        .values {
            margin-bottom: 60px;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .value-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .value-card:hover {
            transform: translateY(-5px);
        }
        
        .value-icon {
            font-size: 40px;
            color: #3498db;
            margin-bottom: 20px;
        }
        
        .value-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #2c3e50;
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
            .about-content, .mission-vision {
                grid-template-columns: 1fr;
            }
            
            .about-hero h1 {
                font-size: 36px;
            }
            
            .section-title h2 {
                font-size: 30px;
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
                <div class="logo">ShopName</div>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php" class="active">About</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Hero Section -->
            <section class="about-hero">
                <h1>About Our Company</h1>
                <p>Learn more about our story, mission, and the team behind ShopName</p>
            </section>
            
            <!-- Our Story Section -->
            <section class="our-story">
                <div class="section-title">
                    <h2>Our Story</h2>
                </div>
                
                <div class="about-content">
                    <div class="about-text">
                        <h3>From Humble Beginnings</h3>
                        <p>Founded in 2010, ShopName started as a small family business with a passion for delivering high-quality products to our community. What began as a single retail store has now grown into a thriving e-commerce platform serving customers nationwide.</p>
                        <p>Our journey hasn't always been easy, but our commitment to quality and customer satisfaction has remained constant. We've weathered economic challenges, adapted to changing technologies, and continuously evolved to meet our customers' needs.</p>
                        <p>Today, we're proud to offer a curated selection of products that we personally believe in and use ourselves. Every item in our store meets our strict standards for quality, durability, and value.</p>
                    </div>
                    <div class="about-image">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSGhyi7HsidiT4JXdv3A6YxLi1ufSMmIVbHbQ&s" alt="Our first store">
                    </div>
                </div>
            </section>
            
            <!-- Mission & Vision Section -->
            <section class="mission-vision-section">
                <div class="section-title">
                    <h2>Our Core</h2>
                </div>
                
                <div class="mission-vision">
                    <div class="mission">
                        <h3><i class="fas fa-bullseye"></i> Our Mission</h3>
                        <p>To provide exceptional quality products at affordable prices while delivering outstanding customer service. We aim to make online shopping easy, enjoyable, and accessible to everyone.</p>
                        <p>We're committed to ethical sourcing, sustainable practices, and building long-term relationships with both our customers and suppliers.</p>
                    </div>
                    
                    <div class="vision">
                        <h3><i class="fas fa-eye"></i> Our Vision</h3>
                        <p>To become the most trusted online shopping destination by continuously innovating our platform, expanding our product offerings, and maintaining our reputation for reliability and integrity.</p>
                        <p>We envision a future where shopping with us isn't just a transaction, but an experience that customers look forward to.</p>
                    </div>
                </div>
            </section>
            
            <!-- Values Section -->
            <section class="values">
                <div class="section-title">
                    <h2>Our Values</h2>
                </div>
                
                <div class="values-grid">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <h3>Customer First</h3>
                        <p>Our customers are at the heart of everything we do. We listen, we care, and we go the extra mile to ensure satisfaction.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <h3>Quality</h3>
                        <p>We never compromise on quality. Every product in our store meets our rigorous standards before we offer it to you.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h3>Innovation</h3>
                        <p>We continuously seek better ways to serve you, from our website features to our delivery options.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h3>Sustainability</h3>
                        <p>We're committed to eco-friendly practices and reducing our environmental footprint at every opportunity.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Community</h3>
                        <p>We believe in giving back and supporting the communities that have supported us throughout our journey.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-gem"></i>
                        </div>
                        <h3>Integrity</h3>
                        <p>We conduct our business with honesty, transparency, and fairness in all our dealings.</p>
                    </div>
                </div>
            </section>
            
            <!-- Team Section -->
            <section class="team">
                <div class="section-title">
                    <h2>Meet Our Team</h2>
                </div>
                
                <div class="team-grid">
                    <div class="team-member">
                        <img src="https://via.placeholder.com/300x300" alt="John Doe">
                        <div class="team-info">
                            <h4>John Doe</h4>
                            <p>Founder & CEO</p>
                            <div class="social-links">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="team-member">
                        <img src="https://via.placeholder.com/300x300" alt="Jane Smith">
                        <div class="team-info">
                            <h4>Jane Smith</h4>
                            <p>Marketing Director</p>
                            <div class="social-links">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="team-member">
                        <img src="https://via.placeholder.com/300x300" alt="Mike Johnson">
                        <div class="team-info">
                            <h4>Mike Johnson</h4>
                            <p>Head of Operations</p>
                            <div class="social-links">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="team-member">
                        <img src="https://via.placeholder.com/300x300" alt="Sarah Williams">
                        <div class="team-info">
                            <h4>Sarah Williams</h4>
                            <p>Customer Service Manager</p>
                            <div class="social-links">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
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