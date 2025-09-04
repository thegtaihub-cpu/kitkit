<?php
session_start();
include_once '../config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get cart items with product details
$cartItems = getCartItems($pdo);
$cartTotal = getCartTotal($pdo);
$cart_count = array_sum($_SESSION['cart']);

// Calculate additional costs
$delivery_fee = 2.99;
$service_fee = 1.50;
$tax_rate = 0.095; // 9.5%
$tax = $cartTotal * $tax_rate;
$subtotal = $cartTotal;
$total = $subtotal + $delivery_fee + $service_fee + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart & Checkout - QuickCart Pro</title>
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
                    <button class="relative p-2 text-primary font-medium">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
                        </svg>
                        <span id="cart-badge" class="absolute -top-1 -right-1 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $cart_count; ?></span>
                    </button>
                    <span class="text-primary font-medium">Cart</span>
                </div>
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

    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-neutral-500">
            <a href="homepage.php" class="hover:text-primary transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-primary font-medium">Shopping Cart</span>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <?php if (empty($cartItems)): ?>
        <!-- Empty Cart -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-24 w-24 text-neutral-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
                </svg>
                <h2 class="text-2xl font-bold text-neutral-800 mb-2">Your cart is empty</h2>
                <p class="text-neutral-600 mb-8">Add some products to get started!</p>
                <a href="homepage.php" class="btn-primary">Continue Shopping</a>
            </div>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items Section -->
            <div class="lg:col-span-2">
                <!-- Cart Header -->
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-3xl font-bold text-primary">Shopping Cart</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-neutral-600"><?php echo count($cartItems); ?> items</span>
                        <button id="clear-cart-btn" class="text-accent hover:text-accent-600 font-medium">Clear Cart</button>
                    </div>
                </div>

                <!-- Delivery Time Banner -->
                <?php if ($cartTotal >= 25): ?>
                <div class="bg-success-50 border border-success-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-success" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-success-700">Great! Your order qualifies for 30-minute delivery</p>
                            <p class="text-success-600 text-sm">Free express delivery for orders over $25</p>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="bg-warning-50 border border-warning-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-warning" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-warning-700">Add $<?php echo number_format(25 - $cartTotal, 2); ?> more for free express delivery</p>
                            <p class="text-warning-600 text-sm">Current order total: $<?php echo number_format($cartTotal, 2); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Cart Items -->
                <div id="cart-items" class="space-y-4">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item card" data-product-id="<?php echo $item['id']; ?>">
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 overflow-hidden rounded-lg">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>?auto=compress&cs=tinysrgb&w=200&h=200&dpr=1" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="text-neutral-500 text-sm"><?php echo htmlspecialchars($item['category']); ?> • <?php echo htmlspecialchars($item['weight']); ?></p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-success text-sm font-medium">✓ In Stock</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <!-- Quantity Controls -->
                                <div class="flex items-center border border-neutral-300 rounded-lg">
                                    <button class="quantity-btn p-2 hover:bg-neutral-100 transition-colors" data-action="decrease" data-product-id="<?php echo $item['id']; ?>">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <span class="px-4 py-2 font-medium quantity-display"><?php echo $item['cart_quantity']; ?></span>
                                    <button class="quantity-btn p-2 hover:bg-neutral-100 transition-colors" data-action="increase" data-product-id="<?php echo $item['id']; ?>">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </button>
                                </div>
                                <!-- Price -->
                                <div class="text-right">
                                    <p class="font-bold text-lg text-primary item-total">$<?php echo number_format($item['subtotal'], 2); ?></p>
                                    <p class="text-neutral-500 text-sm">$<?php echo number_format($item['price'], 2); ?> each</p>
                                </div>
                                <!-- Remove Button -->
                                <button class="remove-item-btn p-2 text-neutral-400 hover:text-accent transition-colors" data-product-id="<?php echo $item['id']; ?>">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Checkout Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <!-- Order Summary -->
                    <div class="card mb-6">
                        <h3 class="text-xl font-bold text-primary mb-4">Order Summary</h3>
                        
                        <!-- Pricing Breakdown -->
                        <div class="space-y-3 mb-4">
                            <div class="flex justify-between">
                                <span class="text-neutral-600">Subtotal (<span id="item-count"><?php echo count($cartItems); ?></span> items)</span>
                                <span id="subtotal-amount">$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-neutral-600">Delivery fee</span>
                                <span>$<?php echo number_format($delivery_fee, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-neutral-600">Service fee</span>
                                <span>$<?php echo number_format($service_fee, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-neutral-600">Tax</span>
                                <span id="tax-amount">$<?php echo number_format($tax, 2); ?></span>
                            </div>
                            <div class="border-t pt-3">
                                <div class="flex justify-between font-bold text-lg">
                                    <span>Total</span>
                                    <span id="total-amount" class="text-primary">$<?php echo number_format($total, 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Options -->
                    <div class="card mb-6">
                        <h3 class="text-xl font-bold text-primary mb-4">Delivery Options</h3>
                        
                        <!-- Delivery Time Slots -->
                        <div class="space-y-3 mb-4">
                            <label class="flex items-center space-x-3 p-3 border border-primary-200 rounded-lg cursor-pointer bg-primary-50">
                                <input type="radio" name="delivery" value="express" class="text-primary" checked />
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold">Express (30 min)</span>
                                        <span class="text-primary font-bold">$2.99</span>
                                    </div>
                                    <p class="text-sm text-neutral-600">Available now</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center space-x-3 p-3 border border-neutral-200 rounded-lg cursor-pointer hover:bg-neutral-50">
                                <input type="radio" name="delivery" value="standard" class="text-primary" />
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold">Standard (1-2 hours)</span>
                                        <span class="text-success font-bold">Free</span>
                                    </div>
                                    <p class="text-sm text-neutral-600">Next available: 2:30 PM</p>
                                </div>
                            </label>
                        </div>

                        <!-- Delivery Address -->
                        <div class="border-t pt-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold">Delivery Address</span>
                                <button class="text-primary text-sm hover:underline">Change</button>
                            </div>
                            <div class="text-sm text-neutral-600">
                                <p>John Smith</p>
                                <p>1234 Pine Street, Apt 5B</p>
                                <p>Seattle, WA 98101</p>
                            </div>
                        </div>
                    </div>

                    <!-- Checkout Buttons -->
                    <div class="space-y-3">
                        <button class="w-full btn-primary text-lg py-4" onclick="proceedToCheckout()">
                            Place Order - <span id="checkout-total">$<?php echo number_format($total, 2); ?></span>
                        </button>
                        <a href="homepage.php" class="w-full border-2 border-neutral-300 text-neutral-700 font-semibold py-3 px-6 rounded-lg hover:bg-neutral-50 transition-colors text-center block">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

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
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Cart Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize cart functionality
            initCartFunctionality();
        });

        function initCartFunctionality() {
            // Quantity buttons
            const quantityButtons = document.querySelectorAll('.quantity-btn');
            quantityButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.getAttribute('data-action');
                    const productId = this.getAttribute('data-product-id');
                    
                    if (action === 'increase') {
                        updateQuantity(productId, 'increase');
                    } else if (action === 'decrease') {
                        updateQuantity(productId, 'decrease');
                    }
                });
            });

            // Remove item buttons
            const removeButtons = document.querySelectorAll('.remove-item-btn');
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    removeItem(productId);
                });
            });

            // Clear cart button
            const clearCartBtn = document.getElementById('clear-cart-btn');
            if (clearCartBtn) {
                clearCartBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to clear your cart?')) {
                        clearCart();
                    }
                });
            }
        }

        async function updateQuantity(productId, action) {
            const cartItem = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
            const quantityDisplay = cartItem.querySelector('.quantity-display');
            let currentQuantity = parseInt(quantityDisplay.textContent);
            
            if (action === 'increase') {
                currentQuantity++;
            } else if (action === 'decrease' && currentQuantity > 1) {
                currentQuantity--;
            } else if (action === 'decrease' && currentQuantity === 1) {
                // Remove item if quantity becomes 0
                removeItem(productId);
                return;
            }

            try {
                const response = await fetch('../api/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'update',
                        product_id: productId,
                        quantity: currentQuantity
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    // Update quantity display
                    quantityDisplay.textContent = currentQuantity;
                    
                    // Update cart totals
                    updateCartTotals();
                    
                    // Update cart badge
                    updateCartBadge(result.cart_count);
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
                showNotification('Error updating cart', 'error');
            }
        }

        async function removeItem(productId) {
            try {
                const response = await fetch('../api/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'remove',
                        product_id: productId
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    // Remove item from DOM
                    const cartItem = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
                    cartItem.remove();
                    
                    // Update cart totals
                    updateCartTotals();
                    
                    // Update cart badge
                    updateCartBadge(result.cart_count);
                    
                    // Check if cart is empty
                    const remainingItems = document.querySelectorAll('.cart-item');
                    if (remainingItems.length === 0) {
                        location.reload();
                    }
                    
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Error removing item:', error);
                showNotification('Error removing item', 'error');
            }
        }

        async function clearCart() {
            try {
                const response = await fetch('../api/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'clear'
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Error clearing cart:', error);
                showNotification('Error clearing cart', 'error');
            }
        }

        function updateCartTotals() {
            let subtotal = 0;
            const cartItems = document.querySelectorAll('.cart-item');
            
            cartItems.forEach(item => {
                const quantityDisplay = item.querySelector('.quantity-display');
                const quantity = parseInt(quantityDisplay.textContent);
                const productId = item.getAttribute('data-product-id');
                
                // Get price from data attribute or calculate
                const priceText = item.querySelector('.text-neutral-500').textContent;
                const price = parseFloat(priceText.match(/\$(\d+\.\d+)/)[1]);
                
                const itemTotal = price * quantity;
                subtotal += itemTotal;
                
                // Update item total display
                const itemTotalElement = item.querySelector('.item-total');
                itemTotalElement.textContent = `$${itemTotal.toFixed(2)}`;
            });
            
            // Update totals
            const deliveryFee = 2.99;
            const serviceFee = 1.50;
            const taxRate = 0.095;
            const tax = subtotal * taxRate;
            const total = subtotal + deliveryFee + serviceFee + tax;
            
            document.getElementById('subtotal-amount').textContent = `$${subtotal.toFixed(2)}`;
            document.getElementById('tax-amount').textContent = `$${tax.toFixed(2)}`;
            document.getElementById('total-amount').textContent = `$${total.toFixed(2)}`;
            document.getElementById('checkout-total').textContent = `$${total.toFixed(2)}`;
            document.getElementById('item-count').textContent = cartItems.length;
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

        function proceedToCheckout() {
            alert('Checkout functionality would be implemented here. This would redirect to a payment gateway or checkout form.');
        }
    </script>
</body>
</html>