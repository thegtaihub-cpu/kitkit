<?php
session_start();
include_once '../config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get cart count
$cart_count = array_sum($_SESSION['cart']);

// Get featured products
$featured_products = getProducts($pdo, 6);
$trending_products = getProductsByCategory($pdo, 'Fresh Produce', 4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>QuickCart Pro - Your neighborhood, delivered instantly</title>
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
</head>
<body class="bg-background">
    <!-- Header Navigation -->
    <header class="bg-white shadow-soft sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="homepage.php" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7ZM9 3V4H15V3H9ZM7 6V19H17V6H7Z"/>
                            <path d="M9 8V17H11V8H9ZM13 8V17H15V8H13Z"/>
                        </svg>
                        <span class="text-xl font-bold text-primary">QuickCart Pro</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="homepage.php" class="text-primary font-medium">Home</a>
                    <a href="product_categories.php" class="text-neutral-600 hover:text-primary transition-colors">Categories</a>
                    <a href="order_tracking.php" class="text-neutral-600 hover:text-primary transition-colors">Track Orders</a>
                    <a href="user_profile_dashboard.php" class="text-neutral-600 hover:text-primary transition-colors">Account</a>
                </nav>

                <!-- Cart and User Actions -->
                <div class="flex items-center space-x-4">
                    <button class="relative p-2 text-neutral-600 hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
                        </svg>
                        <span id="cart-badge" class="absolute -top-1 -right-1 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $cart_count; ?></span>
                    </button>
                    <a href="shopping_cart_checkout.php" class="btn-primary text-sm">Cart</a>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden p-2 text-neutral-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Location Banner -->
    <div class="bg-primary-50 border-b border-primary-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
            <div class="flex items-center justify-center space-x-2 text-sm">
                <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <span class="text-primary-700">Delivering to: <strong>Downtown Seattle, WA 98101</strong></span>
                <button class="text-primary-600 hover:text-primary-700 underline">Change</button>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-primary-50 to-secondary-50 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <img src="https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Fresh produce background" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'; this.onerror=null;" />
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-primary mb-6">
                    Your neighborhood,<br />
                    <span class="text-accent font-caveat text-5xl md:text-7xl">delivered instantly</span>
                </h1>
                <p class="text-xl text-neutral-600 mb-8 max-w-2xl mx-auto">
                    Fresh groceries from local stores delivered to your door in 30 minutes or less. Quality you trust, speed you need.
                </p>

                <!-- Smart Search Bar -->
                <div class="max-w-2xl mx-auto mb-8">
                    <div class="relative">
                        <input type="text" placeholder="Search for groceries, brands, or stores..." class="w-full pl-12 pr-20 py-4 text-lg border-2 border-primary-200 rounded-2xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition-all" />
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <div class="absolute right-2 top-1/2 transform -translate-y-1/2 flex space-x-2">
                            <button class="p-2 text-neutral-400 hover:text-primary transition-colors" title="Voice Search">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"/>
                                    <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                                </svg>
                            </button>
                            <button class="p-2 text-neutral-400 hover:text-primary transition-colors" title="Barcode Scanner">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h2M5 8h2a1 1 0 001-1V4a1 1 0 00-1-1H5a1 1 0 00-1 1v3a1 1 0 001 1zm0 10h2a1 1 0 001-1v-3a1 1 0 00-1-1H5a1 1 0 00-1 1v3a1 1 0 001 1z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Trust Indicators -->
                <div class="flex flex-wrap justify-center items-center gap-6 text-sm text-neutral-600">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-success" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span><strong>4.8/5</strong> Customer Rating</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span><strong>30 min</strong> or less delivery</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-secondary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span><strong>500+</strong> Local Partners</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Reorders Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-primary">Featured Products</h2>
                <a href="product_categories.php" class="text-primary-600 hover:text-primary-700 font-medium">View All →</a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($featured_products as $product): ?>
                <!-- Product Card -->
                <div class="card card-hover cursor-pointer" onclick="window.location.href='product_detail.php?id=<?php echo $product['id']; ?>'">
                    <div class="aspect-square mb-3 overflow-hidden rounded-lg">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>?auto=compress&cs=tinysrgb&w=400&h=400&dpr=1" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?q=80&w=400&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'; this.onerror=null;" />
                    </div>
                    <h3 class="font-semibold text-sm mb-1"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="text-neutral-500 text-xs mb-2"><?php echo htmlspecialchars($product['weight']); ?></p>
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-primary">$<?php echo number_format($product['price'], 2); ?></span>
                        <button class="add-to-cart-btn bg-primary text-white p-1 rounded-full hover:bg-primary-600 transition-colors" data-product-id="<?php echo $product['id']; ?>" onclick="event.stopPropagation()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Trending in Your Area -->
    <section class="py-16 bg-surface">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-primary">Trending in Your Area</h2>
                <a href="product_categories.php" class="text-primary-600 hover:text-primary-700 font-medium">View All →</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($trending_products as $product): ?>
                <!-- Trending Product -->
                <div class="card card-hover" onclick="window.location.href='product_detail.php?id=<?php echo $product['id']; ?>'">
                    <div class="relative">
                        <div class="aspect-w-4 aspect-h-3 mb-4 overflow-hidden rounded-lg">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>?q=80&w=600&auto=format&fit=crop" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover" onerror="this.src='https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&dpr=1'; this.onerror=null;" />
                        </div>
                        <span class="absolute top-2 left-2 bg-accent text-white text-xs px-2 py-1 rounded-full">🔥 Trending</span>
                    </div>
                    <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="text-neutral-500 text-sm mb-3"><?php echo htmlspecialchars(substr($product['description'], 0, 60)); ?>...</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-bold text-xl text-primary">$<?php echo number_format($product['price'], 2); ?></span>
                            <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                            <span class="text-neutral-400 text-sm line-through ml-2">$<?php echo number_format($product['original_price'], 2); ?></span>
                            <?php endif; ?>
                        </div>
                        <button class="add-to-cart-btn btn-primary text-sm" data-product-id="<?php echo $product['id']; ?>" onclick="event.stopPropagation()">Add to Cart</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Express Checkout CTA -->
    <section class="py-16 bg-gradient-to-r from-primary to-primary-700">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Need groceries <span class="text-secondary font-caveat text-4xl md:text-5xl">right now?</span>
            </h2>
            <p class="text-xl text-primary-100 mb-8">
                Express delivery in 15 minutes for essential items. Perfect for busy professionals and last-minute needs.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="product_categories.php" class="bg-white text-primary font-semibold py-4 px-8 rounded-lg hover:bg-neutral-50 transition-colors">
                    Start Express Order
                </a>
                <a href="user_profile_dashboard.php" class="border-2 border-white text-white font-semibold py-4 px-8 rounded-lg hover:bg-white hover:text-primary transition-colors">
                    View Account
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-neutral-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7ZM9 3V4H15V3H9ZM7 6V19H17V6H7Z"/>
                        </svg>
                        <span class="text-2xl font-bold">QuickCart Pro</span>
                    </div>
                    <p class="text-neutral-300 mb-6 max-w-md">
                        Your neighborhood's digital grocery companion. Quality you trust, speed you need, community you love.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="product_categories.php" class="text-neutral-300 hover:text-white transition-colors">Browse Categories</a></li>
                        <li><a href="order_tracking.php" class="text-neutral-300 hover:text-white transition-colors">Track Your Order</a></li>
                        <li><a href="user_profile_dashboard.php" class="text-neutral-300 hover:text-white transition-colors">My Account</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-neutral-300">
                        <li>📞 1-800-QUICKCART</li>
                        <li>📧 support@quickcartpro.com</li>
                        <li>📍 Seattle, WA</li>
                        <li>🕒 24/7 Customer Support</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-neutral-700 mt-12 pt-8 text-center text-neutral-400">
                <p>&copy; 2025 QuickCart Pro. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation (Sticky) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-neutral-200 z-50">
        <div class="flex items-center justify-around py-2">
            <a href="homepage.php" class="flex flex-col items-center p-2 text-primary">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span class="text-xs mt-1">Home</span>
            </a>
            <a href="product_categories.php" class="flex flex-col items-center p-2 text-neutral-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5m14 14H5"/>
                </svg>
                <span class="text-xs mt-1">Categories</span>
            </a>
            <a href="shopping_cart_checkout.php" class="flex flex-col items-center p-2 text-neutral-500 relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
                </svg>
                <span id="mobile-cart-badge" class="absolute -top-1 -right-1 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $cart_count; ?></span>
                <span class="text-xs mt-1">Cart</span>
            </a>
            <a href="order_tracking.php" class="flex flex-col items-center p-2 text-neutral-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-xs mt-1">Track</span>
            </a>
            <a href="user_profile_dashboard.php" class="flex flex-col items-center p-2 text-neutral-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs mt-1">Account</span>
            </a>
        </div>
    </div>

    <!-- JavaScript for Cart Functionality -->
    <script>
        // Add to cart functionality
        document.addEventListener('DOMContentLoaded', function() {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    addToCart(productId);
                });
            });
        });

        async function addToCart(productId, quantity = 1) {
            try {
                const response = await fetch('../api/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'add',
                        product_id: productId,
                        quantity: quantity
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    // Update cart badges
                    updateCartBadges(result.cart_count);
                    
                    // Show success message
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                showNotification('Error adding product to cart', 'error');
            }
        }

        function updateCartBadges(count) {
            const cartBadge = document.getElementById('cart-badge');
            const mobileCartBadge = document.getElementById('mobile-cart-badge');
            
            if (cartBadge) cartBadge.textContent = count;
            if (mobileCartBadge) mobileCartBadge.textContent = count;
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</body>
</html>