<?php

// Start the session
session_start();

// Database connection
include 'connection.php';

// Fetch products
include 'products.php';

// Fetch User
include 'user.php';

// Fetch Cart items
include 'cart.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        ShopAI - 
        <?php
            if (isset($userData['username']) && !empty($userData['username'])) {
                echo htmlspecialchars($userData['username']);
            } else {
                echo "Home";
            }
        ?>
    </title>

    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>

    <link href="styles/style.css" rel="stylesheet">
    <link href="styles/navbar.css" rel="stylesheet">
    <link href="styles/popover.css" rel="stylesheet">
    <link href="styles/containers.css" rel="stylesheet">
    <link href="styles/mobile.css" rel="stylesheet">
    <link href="styles/emmabot.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="navbar">
            <div class="logo-container">
                <div class="logo">ShopAI</div>
                <button class="hamburger-menu" id="hamburger-menu">☰</button>
            </div>

            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Enter product name..." oninput="fetchSuggestions()">
                <div id="suggestions" class="suggestions-container"></div>
                <button onclick="scrollToProduct()">Search</button>
            </div>

            <ul class="nav-links" id="nav-links">
                <li><a href="#" id="home">Home</a></li>
                <li><a href="#products-section">Products</a></li>
                <li><a href="#services-section" id="services">Services</a></li>
                <li><a href="#brands-section" id="brands">Brands</a></li>
                <li><a href="#about-section" id="about">About</a></li>
                <li><a href="#faq-section" id="faq">FAQ</a></li>
                <li><a href="#contact-section" id="contact">Contact</a></li>
            </ul>

            <div class="top-container">
                <div class="profile-container">
                    <?php if ($isLoggedIn): ?>
                    <button popovertarget="profilePopover" id="profileButton" class="profile-icon">
                        <?php
                            $profileImg = 'img/nopic.jpg';
                            if (!empty($userData['profile_img'])) {
                                $profileImg = 'data:image/jpeg;base64,' . base64_encode($userData['profile_img']);
                            }
                        ?>
                        <img src="<?php echo htmlspecialchars($profileImg); ?>" alt="Profile" class="profile-img">
                    </button>
                    <?php else: ?>
                        <button popovertarget="userPopover" id="userButton" class="user-icon">👤</button>
                    <?php endif; ?>
                </div>
                <div class="cart-container">
                    <button popovertarget="cartPopover" id="cartButton" class="cart-icon">
                        🛒 <span id="cartCount">0</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Header Popovers -->
        <div popover id="userPopover" class="user-popover-container">
            <button popovertarget="userPopover" popovertargetaction="hide" class="user-close-btn" aria-label="Close">&times;</button>
            <h3>User Options</h3>
            <button class="user-btn" popovertarget="loginPopover">Login</button>
            <button class="user-btn" popovertarget="signupPopover">Signup</button>
        </div>

        <div popover id="loginPopover" class="login-popover-container">
            <div class="login-modal-content">
                <button popovertarget="loginPopover" popovertargetaction="hide" class="login-close-btn" aria-label="Close">&times;</button>
                <h3>Login</h3>
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="login">
                    <label for="loginUsername">Username</label>
                    <input type="text" id="loginUsername" name="username" placeholder="Enter your username" required>
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
                    <button type="submit" class="login-btn">Login</button>
                </form>
            </div>
        </div>

        <div popover id="signupPopover" class="signup-popover-container">
            <div class="signup-modal-content">
                <button popovertarget="signupPopover" popovertargetaction="hide" class="signup-close-btn" aria-label="Close">&times;</button>
                <h3>Signup</h3>
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="signup">
                    <label for="signupUsername">Username</label>
                    <input type="text" id="signupUsername" name="username" placeholder="Enter your username" required>
                    <label for="signupEmail">Email</label>
                    <input type="email" id="signupEmail" name="email" placeholder="Enter your email" required>
                    <label for="signupPassword">Password</label>
                    <input type="password" id="signupPassword" name="password" placeholder="Enter your password" required>
                    <button type="submit" class="signup-btn">Signup</button>
                </form>
            </div>
        </div>

        <div popover id="profilePopover" class="profile-popover-container">
                <button popovertarget="profilePopover" popovertargetaction="hide" class="profile-close-btn" aria-label="Close">&times;</button>
                <h3>Profile</h3>
                <?php if ($isLoggedIn && $userData): ?>
                    <div class="profile-info">
                        <?php
                            $profileImg = 'img/nopic.jpg';
                            if (!empty($userData['profile_img'])) {
                                $profileImg = 'data:image/jpeg;base64,' . base64_encode($userData['profile_img']);
                            }
                        ?>
                        <img src="<?php echo htmlspecialchars($profileImg); ?>" alt="Profile" class="profile-img">
                        <p><strong>Username:</strong></p>
                        <p><?php echo htmlspecialchars($userData['username']); ?></p>
                        <p><strong>Email:</strong></p>
                        <p><?php echo htmlspecialchars($userData['email']); ?></p>
                        <p><strong>Birthday:</strong></p>
                        <p><?php echo htmlspecialchars($userData['birthday'] ?? ''); ?></p>
                        <p><strong>Address:</strong></p>
                        <p><?php echo htmlspecialchars($userData['address'] ?? ''); ?></p>
                    </div>
                    <div>
                        <button popovertarget="buyOrdersPopover" class="profile-btn">My Orders</button>
                        <button popovertarget="editPopover" class="profile-btn">Edit Profile</button>
                        <form method="POST" action="index.php">
                            <input type="hidden" name="action" value="logout">
                            <button type="submit" class="profile-btn">Logout</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        
        <div popover id="buyOrdersPopover" class="buyorder-popover-container">
            <button popovertarget="buyOrdersPopover" popovertargetaction="hide" class="buyorder-close-btn" aria-label="Close">&times;</button>
            <h3>My Orders</h3>
            <div class="orders-list">
                <?php
                if ($isLoggedIn) {
                    // Fetch orders for this user
                    $userId = $_SESSION['user_id'];
                    $ordersSql = "
                        SELECT ps.*, p.name AS product_name, p.price, p.product_img
                        FROM product_status ps
                        JOIN products p ON ps.product_id = p.id
                        WHERE ps.user_id = ?
                        ORDER BY ps.order_time DESC
                    ";
                    $stmt = $conn->prepare($ordersSql);
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $ordersResult = $stmt->get_result();
                    if ($ordersResult->num_rows > 0) {
                        while ($order = $ordersResult->fetch_assoc()) {
                            $imgSrc = 'img/nopic.jpg';
                            if (!empty($order['product_img'])) {
                                $imgSrc = 'data:image/jpeg;base64,' . base64_encode($order['product_img']);
                            }
                            echo '<div class="order-row">';
                            echo '<img src="'.htmlspecialchars($imgSrc).'" alt="Product" class="order-img">';
                            echo '<div style="flex:1;">';
                            echo '<div style="font-weight:bold;">'.htmlspecialchars($order['product_name']).'</div>';
                            echo '<div style="color:#569c71;">₱'.number_format($order['price'],2).'</div>';
                            echo '<div style="font-size:0.95em;">Status: <span class="status-badge '.htmlspecialchars($order['status']).'">'.ucfirst($order['status']).'</span></div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p style="text-align:center;">No orders found.</p>';
                    }
                    $stmt->close();
                } else {
                    echo '<p style="text-align:center;">Please log in to view your orders.</p>';
                }
                ?>
            </div>
        </div>

        <div popover id="editPopover" class="edit-popover-container">
            <div class="edit-modal-content">
                <button popovertarget="editPopover" popovertargetaction="hide" class="edit-close-btn" aria-label="Close">&times;</button>
                <h3>Edit Profile</h3>
                <form method="POST" action="index.php" enctype="multipart/form-data" onsubmit="return confirmProfileUpdate();">
                    <input type="hidden" name="action" value="edit">
                    <label for="editUsername">Username</label>
                    <input type="text" id="editUsername" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>">

                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>">
                    
                    <label for="editPassword">New Password</label>
                    <input type="password" id="editPassword" name="password" placeholder="Leave blank to keep current">
                    
                    <label for="editAddress">Address</label>
                    <input type="text" id="editAddress" name="address" value="<?php echo htmlspecialchars($userData['address'] ?? ''); ?>">
                    
                    <label for="editBirthday">Birthday</label>
                    <input type="date" id="editBirthday" name="birthday" value="<?php echo htmlspecialchars($userData['birthday'] ?? ''); ?>">
                    
                    <label for="editImage">Profile Image</label>
                    <input type="file" id="editImage" name="profile_img" accept="image/*">
                    
                    <button type="submit" class="edit-btn">Save Changes</button>
                </form>
            </div>
        </div>

        <div popover id="cartPopover" class="cart-popover-container">
            <button popovertarget="cartPopover" popovertargetaction="hide" class="cart-close-btn" aria-label="Close">&times;</button>
            <h3>Shopping Cart</h3>
            <ul id="cartItems"></ul>
            <p id="cartTotal">Total: &#8369;0.00</p>
            <button onclick="clearCart()" class="cart-button">Clear Cart</button>
            <button onclick="purchaseCart()" class="cart-button">Purchase</button>
        </div>

        <!-- Main Content -->
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
                        
                        // Convert image data to base64
                        $imageData = base64_encode($row['product_img']);
                        $imageSrc = "data:product_img/jpeg;base64," . $imageData;
                        ?>
                        <div class="product" 
                            data-id="<?php echo $row['id']; ?>"
                            data-name="<?php echo $row['name']; ?>" 
                            data-price="<?php echo $row['price']; ?>" 
                            data-img="<?php echo $imageSrc; ?>" 
                            data-description="<?php echo htmlspecialchars($row['description']); ?>">
                            <img src="<?php echo $imageSrc; ?>" alt="<?php echo $row['name']; ?>" class="product-img">
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
        <div id="productModal" class="product-modal">
            <div class="product-modal-content" id="productModal">
                <span class="product-close-btn" onclick="closeModal()">&times;</span>
                <img id="modalImage" src="" alt="Product Image">
                <h3 id="modalTitle">Product Name</h3>
                <h5 id="modalDescription">Product Description</h5>
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
                    <p>Email: support@shopai.com</p>
                    <p>Phone: +9673280015</p>
                    <p>Address: WCQV+8H9, Tagudin, 2714 Ilocos Sur</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 ShopAI. All rights reserved.</p>
            </div>
        </footer>

        <!-- Emma bot -->
        <div class="chatbot-button-container">
            <button popovertarget="chat-container" class="chatbot-button ani">AskEmma</button>
        </div>

        <div popover id="chat-container" class="chatbot-chat-container">
            <button popovertarget="chat-container" popovertargetaction="hide" class="chat-close-btn" aria-label="Close">&times;</button>
            <h2 class="emmatag">EmmaAI you're Assistant</h2>
            <div class="message-box" id="messagebox"></div>
            <input class="user-input" type="text" id="userinput" placeholder="Type your message here...">
            <button class="user-button" onclick="sendMessage()">Send</button>
         </div>

        <!-- Back to Top Button -->
        <button class="back-to-top" id="back-to-top" onclick="scrollToTop()">Back to Top</button>
    </div>

    <script>
        var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
    </script>
    <script src="scripts/script.js"></script>
    <script src="scripts/navbar.js"></script>
    <script src="scripts/popover.js"></script>
    <script src="scripts/emmabot.js"></script>
</body>
</html>