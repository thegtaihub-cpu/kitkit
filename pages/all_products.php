<?php
session_start();
include_once '../config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get cart count
$cart_count = array_sum($_SESSION['cart']);

// Get all products
$products = getProducts($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>All Products - KITKIT Shopping</title>
    <link rel="stylesheet" href="../css/main.css" />
</head>
<body class="bg-background">
    <!-- Header Navigation -->
    <header class="bg-white shadow-soft sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="homepage.php" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7ZM9 3V4H15V3H9ZM7 6V19H17V6H7Z"/>
                        </svg>
                        <span class="text-xl font-bold text-primary">KITKIT Shopping</span>
                    </a>
                </div>

                <nav class="hidden md:flex items-center space-x-8">
                    <a href="homepage.php" class="text-neutral-600 hover:text-primary transition-colors">Home</a>
                    <a href="product_categories.php" class="text-neutral-600 hover:text-primary transition-colors">Categories</a>
                    <a href="order_tracking.php" class="text-neutral-600 hover:text-primary transition-colors">Track Orders</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="user_profile_dashboard.php" class="text-neutral-600 hover:text-primary transition-colors">My Account</a>
                    <?php else: ?>
                    <a href="login.php" class="text-neutral-600 hover:text-primary transition-colors">Login</a>
                    <?php endif; ?>
                </nav>

                <div class="flex items-center space-x-4">
                    <a href="shopping_cart_checkout.php" class="relative p-2 text-neutral-600 hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $cart_count; ?></span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="bg-gradient-to-br from-primary-50 to-secondary-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-primary mb-4">All Products</h1>
                <p class="text-xl text-neutral-600 max-w-2xl mx-auto">
                    Discover our complete range of fresh groceries and daily essentials
                </p>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                <div class="card card-hover" onclick="window.location.href='product_detail.php?id=<?php echo $product['id']; ?>'">
                    <div class="aspect-w-4 aspect-h-3 mb-4 overflow-hidden rounded-lg">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover" />
                    </div>
                    <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="text-neutral-500 text-sm mb-3"><?php echo htmlspecialchars(substr($product['description'], 0, 60)); ?>...</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-bold text-xl text-primary">₹<?php echo number_format($product['price'], 2); ?></span>
                            <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                            <span class="text-neutral-400 text-sm line-through ml-2">₹<?php echo number_format($product['original_price'], 2); ?></span>
                            <?php endif; ?>
                        </div>
                        <button class="add-to-cart-btn btn-primary text-sm" data-product-id="<?php echo $product['id']; ?>" onclick="event.stopPropagation()">Add to Cart</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

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
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                showNotification('Error adding product to cart', 'error');
            }
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