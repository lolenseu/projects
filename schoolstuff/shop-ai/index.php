<!-- filepath: /home/lolenseu/VScode/projects/schoolstuff/shopai.com/index.php -->

<?php
// Database connection
include 'connection.php';

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Check if the user is logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'signup' && isset($_POST['username'], $_POST['email'], $_POST['password'])) {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

            $checkUser = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $checkUser->bind_param("s", $email);
            $checkUser->execute();
            $result = $checkUser->get_result();

            if ($result->num_rows > 0) {
                echo "<script>alert('User already exists');</script>";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $password);

                if ($stmt->execute()) {
                    echo "<script>alert('User registered successfully');</script>";
                } else {
                    echo "<script>alert('Failed to register user');</script>";
                }
            }
        } elseif ($action === 'login' && isset($_POST['email'], $_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    echo "<script>alert('Login successful');</script>";
                    // Set session or cookie for logged-in user
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                } else {
                    echo "<script>alert('Invalid password');</script>";
                }
            } else {
                echo "<script>alert('User not found');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopAI</title>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link href="styles.css" rel="stylesheet">
    <link rel="icon" href="img/ShopAI.png" type="image/png">
</head>
<body>
    <div class="container">
        <header class="navbar">
            <div class="logo-container">
                <div class="logo">ShopAI</div>
                <button class="hamburger-menu" id="hamburger-menu">☰</button>
            </div>

            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Enter product name...">
                <button onclick="scrollToProduct()">Search</button>
            </div>

            <ul class="nav-links" id="nav-links">
                <li><a href="index.html" id="home">Home</a></li>
                <li><a href="#products-section">Products</a></li>
                <li><a href="#services-section" id="services">Services</a></li>
                <li><a href="#brands-section" id="brands">Brands</a></li>
                <li><a href="#about-section" id="about">About</a></li>
                <li><a href="#faq-section" id="faq">FAQ</a></li>
                <li><a href="#contact-section" id="contact">Contact</a></li>
                <li>
                    <button id="userButton" class="user-icon" onclick="toggleUserPopover()">👤</button>
                </li>
                <li>
                    <button id="cartButton" class="cart-icon" onclick="toggleCartPopover()">
                        🛒 <span id="cartCount">0</span>
                    </button>
                </li>
            </ul>

            <div id="userPopover" class="user-popover hidden">
                <h3>Options</h3>
                <button class="dropdown-btn" onclick="showLoginPopover()">Login</button>
                <button class="dropdown-btn" onclick="showSignupPopover()">Signup</button>
            </div>

            <div id="cartPopover" class="cart-popover hidden">
                <h3>Shopping Cart</h3>
                <ul id="cartItems"></ul>
                <p id="cartTotal">Total: &#8369;0.00</p>
                <button onclick="clearCart()">Clear Cart</button>
            </div>
        </header>

        <div class="first-container">
            <div class="slideshow-container">
                <div class="mySlides fade">
                    <img src="img/product1.jpeg">
                </div>
                <div class="mySlides fade">
                    <img src="img/product2.jpeg">
                </div>
                <div class="mySlides fade">
                    <img src="img/product3.jpg">
                </div>
                <div class="mySlides fade">
                    <img src="img/product4.jpg">
                </div>
                <div class="mySlides fade">
                    <img src="img/product5.jpeg">
                </div>
            </div>
            <br>
            <div class="dots-container">
                <span class="dot" onclick="currentSlide(1)"></span> 
                <span class="dot" onclick="currentSlide(2)"></span> 
                <span class="dot" onclick="currentSlide(3)"></span> 
                <span class="dot" onclick="currentSlide(4)"></span>
                <span class="dot" onclick="currentSlide(5)"></span> 
            </div>
        </div>

        <div class="second-container" id="products-section">
            <h2 class="section-title">Our Products</h2>
            <div class="product-grid">
            <?php
                if ($result->num_rows > 0) {
                    $count = 0;
                    while ($row = $result->fetch_assoc()) {
                        if ($count % 3 == 0) {
                            echo '<div class="product-row">';
                        }
                        ?>
                        <div class="product" data-name="<?php echo $row['name']; ?>" data-price="<?php echo $row['price']; ?>" data-img="<?php echo $row['image']; ?>">
                            <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="product-img">
                            <h3><?php echo $row['name']; ?></h3>
                            <p>&#8369;<?php echo number_format($row['price'], 2); ?></p>
                        </div>
                        <?php
                        $count++;
                        if ($count % 3 == 0) {
                            echo '</div>';
                        }
                    }
                    if ($count % 3 != 0) {
                        echo '</div>';
                    }
                } else {
                    echo "<p>No products available.</p>";
                }
                ?>
            </div>
        </div>

        <!-- Modal Product -->
        <div id="productModal" class="custom-modal">
            <div class="custom-modal-content">
              <span class="custom-close-btn" onclick="closeModal()">&times;</span>
              <img id="modalImage" src="" alt="Product Image">
              <h3 id="modalTitle">Product Name</h3>
              <p id="modalPrice">&#8369;0.00</p>
              <button class="add-to-cart-btn" onclick="addToCart()">Add to Cart</button>
            </div>
          </div>

        <div class="third-container" id="services-section">
            <h2 class="section-title">Our Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <i class="service-icon"></i>
                    <h3>Fast Delivery</h3>
                    <p>We offer fast and reliable delivery services to ensure your products reach you on time.</p>
                </div>
                <div class="service-card">
                    <i class="service-icon"></i>
                    <h3>Customer Support</h3>
                    <p>Our customer support team is available 24/7 to assist you with any queries or issues.</p>
                </div>
                <div class="service-card">
                    <i class="service-icon"></i>
                    <h3>Easy Returns</h3>
                    <p>We provide hassle-free returns and exchanges for your convenience.</p>
                </div>
                <div class="service-card">
                    <i class="service-icon"></i>
                    <h3>Secure Payment</h3>
                    <p>Our payment gateway is secure and encrypted to protect your financial information.</p>
                </div>
                <div class="service-card">
                    <i class="service-icon"></i>
                    <h3>Gift Wrapping</h3>
                    <p>We offer gift wrapping services to make your presents extra special.</p>
                </div>
                <div class="service-card">
                    <i class="service-icon"></i>
                    <h3>Exclusive Offers</h3>
                    <p>Enjoy exclusive offers and discounts on your favorite products.</p>
                </div>
            </div>
        </div>

        <div class="fourth-container" id="brands-section">
            <h2 class="section-title">Available Brands</h2>
            <div class="brands-row">
                <div class="brand">
                    <img src="img/brand1.jpg" alt="Brand 1">
                </div>
                <div class="brand">
                    <img src="img/brand2.jpg" alt="Brand 2">
                </div>
                <div class="brand">
                    <img src="img/brand3.png" alt="Brand 3">
                </div>
                <div class="brand">
                    <img src="img/brand4.jpg" alt="Brand 4">
                </div>
                <div class="brand">
                    <img src="img/brand5.png" alt="Brand 5">
                </div>
            </div>
        </div>

        <footer>
            <div class="footer-content">
                <div class="footer-section about" id="about-section">
                    <h2>About Us</h2>
                    <p>Welcome to ShopAI! We are dedicated to providing you with the best online shopping experience. Our team is passionate about delivering high-quality products and exceptional customer service. We believe in the power of technology to make shopping easier and more enjoyable for everyone.</p>
                    <p>At ShopAI, we offer a wide range of products to meet your needs. From the latest gadgets to everyday essentials, we have something for everyone. Our mission is to bring you the best products at competitive prices, with fast and reliable delivery.</p>
                    <p>Thank you for choosing ShopAI. We look forward to serving you!</p>
                </div>
                <div class="footer-section faq" id="faq-section">
                    <h2>FAQ</h2>
                    <ul>
                        <li><a href="#faq1">How do I place an order?</a></li>
                        <li><a href="#faq2">What payment methods do you accept?</a></li>
                        <li><a href="#faq3">How can I track my order?</a></li>
                        <li><a href="#faq4">What is your return policy?</a></li>
                    </ul>
                </div>
                <div class="footer-section contact" id="contact-section">
                    <h2>Contact Us</h2>
                    <p>Email: support@ShopAI.com</p>
                    <p>Phone: +9673280015</p>
                    <p>Address: WCQV+8H9, Tagudin, 2714 Ilocos Sur</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 ShopAI. All rights reserved.</p>
            </div>
        </footer>

        <button class="back-to-top" id="back-to-top" onclick="scrollToTop()">Back to Top</button>
    </div>

    <script src="script.js"></script>
</body>
</html>