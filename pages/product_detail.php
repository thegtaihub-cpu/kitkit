<?php
session_start();
include_once '../config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: homepage.php');
    exit;
}

// Get product details
$product = getProductById($pdo, $product_id);

if (!$product) {
    header('Location: homepage.php');
    exit;
}

// Get related products (same category)
$related_products = getProductsByCategory($pdo, $product['category'], 6);
$related_products = array_filter($related_products, function($p) use ($product_id) {
    return $p['id'] != $product_id;
});
$related_products = array_slice($related_products, 0, 4);

// Get cart count
$cart_count = array_sum($_SESSION['cart']);

// Get reviews for this product
$stmt = $pdo->prepare("SELECT r.*, u.first_name, u.last_name FROM reviews r LEFT JOIN users u ON r.user_id = u.id WHERE r.product_id = ? AND r.status = 'active' ORDER BY r.created_at DESC LIMIT 10");
$stmt->execute([$product_id]);
$reviews = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($product['name']); ?> - QuickCart Pro</title>
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
                    <a href="homepage.php" class="text-neutral-600 hover:text-primary transition-colors">Home</a>
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

    <!-- Breadcrumb Navigation -->
    <div class="bg-surface border-b border-neutral-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="homepage.php" class="text-neutral-500 hover:text-primary transition-colors">Home</a>
                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="product_categories.php" class="text-neutral-500 hover:text-primary transition-colors"><?php echo htmlspecialchars($product['category']); ?></a>
                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-primary font-medium"><?php echo htmlspecialchars($product['name']); ?></span>
            </nav>
        </div>
    </div>

    <!-- Product Detail Section -->
    <section class="py-8 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                <!-- Product Images -->
                <div class="space-y-4">
                    <!-- Main Image -->
                    <div class="relative bg-white rounded-2xl overflow-hidden shadow-soft">
                        <div class="aspect-square">
                            <img id="mainImage" src="<?php echo htmlspecialchars($product['image_url']); ?>?q=80&w=800&auto=format&fit=crop" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover cursor-zoom-in" onerror="this.src='https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg?auto=compress&cs=tinysrgb&w=800&h=800&dpr=1'; this.onerror=null;" />
                        </div>
                        
                        <!-- Quality Badge -->
                        <?php if ($product['is_organic']): ?>
                        <div class="absolute top-4 left-4 bg-success text-white px-3 py-1 rounded-full text-sm font-medium">
                            🌱 Organic Certified
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Information -->
                <div class="space-y-6">
                    <!-- Product Title and Rating -->
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-primary mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <p class="text-lg text-neutral-600 mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                        
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="flex items-center space-x-1">
                                <div class="flex text-yellow-400">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <svg class="w-5 h-5 <?php echo $i <= floor($product['rating']) ? 'fill-current' : 'text-neutral-300'; ?>" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-sm text-neutral-600 ml-2"><?php echo $product['rating']; ?> (<?php echo $product['review_count']; ?> reviews)</span>
                            </div>
                            <span class="text-sm text-success-600 font-medium">✓ Verified Quality</span>
                        </div>
                    </div>

                    <!-- Price and Savings -->
                    <div class="bg-surface p-6 rounded-xl">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <span class="text-3xl font-bold text-primary">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                <span class="text-lg text-neutral-400 line-through ml-2">$<?php echo number_format($product['original_price'], 2); ?></span>
                                <span class="bg-accent text-white text-sm px-2 py-1 rounded-full ml-2">
                                    <?php echo round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>% OFF
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-neutral-600">Per <?php echo htmlspecialchars($product['weight']); ?></p>
                                <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                <p class="text-xs text-success-600">Save $<?php echo number_format($product['original_price'] - $product['price'], 2); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity and Add to Cart -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <label class="text-sm font-medium text-neutral-700">Quantity:</label>
                            <div class="flex items-center border border-neutral-300 rounded-lg">
                                <button id="decrease-qty" class="p-2 hover:bg-neutral-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <input id="quantity" type="number" value="1" min="1" max="20" class="w-16 text-center border-0 focus:outline-none" />
                                <button id="increase-qty" class="p-2 hover:bg-neutral-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button id="add-to-cart-btn" class="btn-primary w-full flex items-center justify-center space-x-2" data-product-id="<?php echo $product['id']; ?>">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
                                </svg>
                                <span>Add to Cart</span>
                            </button>
                            <button class="border-2 border-primary text-primary font-semibold py-3 px-6 rounded-lg hover:bg-primary-50 transition-colors flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span>Wishlist</span>
                            </button>
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="space-y-4 border-t pt-4">
                        <h3 class="font-semibold text-neutral-700">Product Information:</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-neutral-600">Category:</span>
                                <span class="font-medium ml-2"><?php echo htmlspecialchars($product['category']); ?></span>
                            </div>
                            <div>
                                <span class="text-neutral-600">Weight:</span>
                                <span class="font-medium ml-2"><?php echo htmlspecialchars($product['weight']); ?></span>
                            </div>
                            <?php if ($product['is_organic']): ?>
                            <div>
                                <span class="text-neutral-600">Certification:</span>
                                <span class="font-medium ml-2 text-success-600">Organic Certified</span>
                            </div>
                            <?php endif; ?>
                            <div>
                                <span class="text-neutral-600">Stock:</span>
                                <span class="font-medium ml-2 <?php echo $product['stock_quantity'] > 10 ? 'text-success-600' : 'text-warning-600'; ?>">
                                    <?php echo $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Reviews -->
    <?php if (!empty($reviews)): ?>
    <section class="py-12 bg-surface">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-primary mb-8">Customer Reviews</h2>
            <div class="space-y-4">
                <?php foreach ($reviews as $review): ?>
                <div class="bg-white p-6 rounded-xl shadow-soft">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="font-semibold text-primary">
                                    <?php echo strtoupper(substr($review['first_name'] ?? 'U', 0, 1)) . strtoupper(substr($review['last_name'] ?? 'ser', 0, 1)); ?>
                                </span>
                            </div>
                            <div>
                                <p class="font-medium"><?php echo htmlspecialchars($review['first_name'] ?? 'User'); ?> <?php echo htmlspecialchars(substr($review['last_name'] ?? '', 0, 1) . '.'); ?></p>
                                <div class="flex items-center space-x-2">
                                    <div class="flex text-yellow-400">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <svg class="w-4 h-4 <?php echo $i <= $review['rating'] ? 'fill-current' : 'text-neutral-300'; ?>" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-sm text-neutral-500"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                                    <?php if ($review['is_verified_purchase']): ?>
                                    <span class="bg-success-100 text-success-700 text-xs px-2 py-1 rounded-full">✓ Verified Purchase</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-neutral-700"><?php echo htmlspecialchars($review['comment']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-primary">You Might Also Like</h2>
                <a href="product_categories.php" class="text-primary-600 hover:text-primary-700 font-medium">View All →</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($related_products as $related_product): ?>
                <div class="card card-hover" onclick="window.location.href='product_detail.php?id=<?php echo $related_product['id']; ?>'">
                    <div class="aspect-w-4 aspect-h-3 mb-4 overflow-hidden rounded-lg">
                        <img src="<?php echo htmlspecialchars($related_product['image_url']); ?>?q=80&w=600&auto=format&fit=crop" alt="<?php echo htmlspecialchars($related_product['name']); ?>" class="w-full h-48 object-cover" />
                    </div>
                    <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($related_product['name']); ?></h3>
                    <p class="text-neutral-500 text-sm mb-3"><?php echo htmlspecialchars(substr($related_product['description'], 0, 60)); ?>...</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-bold text-xl text-primary">$<?php echo number_format($related_product['price'], 2); ?></span>
                        </div>
                        <button class="add-to-cart-btn btn-primary text-sm" data-product-id="<?php echo $related_product['id']; ?>" onclick="event.stopPropagation()">Add to Cart</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-neutral-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
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
                <div>
                    <h3 class="font-semibold text-lg mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="product_categories.php" class="text-neutral-300 hover:text-white transition-colors">Browse Categories</a></li>
                        <li><a href="order_tracking.php" class="text-neutral-300 hover:text-white transition-colors">Track Your Order</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-neutral-300">
                        <li>📞 1-800-QUICKCART</li>
                        <li>📧 support@quickcartpro.com</li>
                        <li>📍 Seattle, WA</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Product Detail Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.getElementById('quantity');
            const decreaseBtn = document.getElementById('decrease-qty');
            const increaseBtn = document.getElementById('increase-qty');
            const addToCartBtn = document.getElementById('add-to-cart-btn');
            
            // Quantity controls
            decreaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue < 20) {
                    quantityInput.value = currentValue + 1;
                }
            });
            
            // Add to cart functionality
            addToCartBtn.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const quantity = parseInt(quantityInput.value);
                addToCart(productId, quantity);
            });
            
            // Related product add to cart buttons
            const relatedCartButtons = document.querySelectorAll('.add-to-cart-btn');
            relatedCartButtons.forEach(button => {
                if (button !== addToCartBtn) {
                    button.addEventListener('click', function() {
                        const productId = this.getAttribute('data-product-id');
                        addToCart(productId);
                    });
                }
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
                    // Update cart badge
                    updateCartBadge(result.cart_count);
                    
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

        function updateCartBadge(count) {
            const cartBadge = document.getElementById('cart-badge');
            if (cartBadge) cartBadge.textContent = count;
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