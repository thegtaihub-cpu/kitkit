<?php
session_start();
include_once '../config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get cart count
$cart_count = array_sum($_SESSION['cart']);

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$products = [];

if (!empty($search)) {
    $products = getProducts($pdo, $search);
} else {
    // Get featured products
    $featured_products = getProducts($pdo, '', '', 6);
    $trending_products = getProductsByCategory($pdo, 'Fresh Fruits', 4);
}

// Get categories
$categories = getCategories($pdo);

// Get banner images
$stmt = $pdo->prepare("SELECT * FROM banner_images WHERE status = 'active' ORDER BY sort_order ASC");
$stmt->execute();
$banners = $stmt->fetchAll();

// Get user location
$user_location = $_SESSION['user_location'] ?? ['city' => 'Mumbai', 'pincode' => '400001', 'state' => 'Maharashtra'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KITKIT Shopping - Fresh Groceries Delivered</title>
    <link rel="stylesheet" href="../css/main.css" />
    <meta name="description" content="KITKIT Shopping - Order fresh groceries, fruits, vegetables online. Fast delivery across India. Best prices guaranteed.">
    <meta name="keywords" content="online grocery India, fresh fruits delivery, vegetables online, dairy products, KITKIT Shopping">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    
    <!-- WhatsApp Chat Widget Styles -->
    <style>
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
            animation: pulse 2s infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .whatsapp-float:hover {
            background-color: #128c7e;
            color: white;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(37, 211, 102, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }
        
        .banner-slider {
            overflow: hidden;
            white-space: nowrap;
            position: relative;
        }
        
        .banner-slide {
            display: inline-block;
            width: 100%;
            animation: slide 15s infinite linear;
        }
        
        @keyframes slide {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
    </style>
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
                        </svg>
                        <span class="text-xl font-bold text-primary">KITKIT Shopping</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="homepage.php" class="text-primary font-medium">Home</a>
                    <a href="product_categories.php" class="text-neutral-600 hover:text-primary transition-colors">Categories</a>
                    <a href="order_tracking.php" class="text-neutral-600 hover:text-primary transition-colors">Track Orders</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="user_profile_dashboard.php" class="text-neutral-600 hover:text-primary transition-colors">My Account</a>
                    <a href="logout.php" class="text-neutral-600 hover:text-primary transition-colors">Logout</a>
                    <?php else: ?>
                    <a href="login.php" class="text-neutral-600 hover:text-primary transition-colors">Login</a>
                    <a href="register.php" class="text-neutral-600 hover:text-primary transition-colors">Register</a>
                    <?php endif; ?>
                </nav>

                <!-- Location Display -->
                <div class="hidden md:flex items-center space-x-2 text-sm">
                    <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span class="text-primary-700"><?php echo htmlspecialchars($user_location['city']); ?>, <?php echo htmlspecialchars($user_location['pincode']); ?></span>
                    <button onclick="changeLocation()" class="text-primary-600 hover:text-primary-700 underline">Change</button>
                </div>

                <!-- Cart and User Actions -->
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="wishlist.php" class="relative p-2 text-neutral-600 hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </a>
                    <?php endif; ?>
                    <a href="shopping_cart_checkout.php" class="relative p-2 text-neutral-600 hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
                        </svg>
                        <span id="cart-badge" class="absolute -top-1 -right-1 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $cart_count; ?></span>
                    </a>
                    <a href="shopping_cart_checkout.php" class="btn-primary text-sm">Cart</a>
                </div>
            </div>
        </div>
    </header>

    <?php if (empty($search)): ?>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-primary-50 to-secondary-50 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <img src="https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Fresh produce background" class="w-full h-full object-cover" />
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-primary mb-6">
                    Fresh groceries,<br />
                    <span class="text-accent font-caveat text-5xl md:text-7xl">delivered instantly</span>
                </h1>
                <p class="text-xl text-neutral-600 mb-8 max-w-2xl mx-auto">
                    Fresh groceries from local stores delivered to your door across India. Quality you trust, speed you need.
                </p>

                <!-- Smart Search Bar -->
                <div class="max-w-2xl mx-auto mb-8">
                    <form action="homepage.php" method="GET" class="relative">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search for groceries, fruits, vegetables..." class="w-full pl-12 pr-20 py-4 text-lg border-2 border-primary-200 rounded-2xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition-all" />
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-600 transition-colors">
                            Search
                        </button>
                    </form>
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
                        <span><strong>Same Day</strong> Delivery</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-secondary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span><strong>500+</strong> Cities Covered</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Auto-scrolling Banner Section -->
    <?php if (!empty($banners)): ?>
    <section class="py-8 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-primary mb-6 text-center">Special Offers & Discounts</h2>
            <div class="banner-slider">
                <?php foreach ($banners as $banner): ?>
                <div class="banner-slide">
                    <a href="<?php echo htmlspecialchars($banner['link_url']); ?>">
                        <img src="<?php echo htmlspecialchars($banner['image_url']); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>" class="w-full h-32 object-cover rounded-lg" />
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Featured Products Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-primary">Featured Products</h2>
                <a href="all_products.php" class="text-primary-600 hover:text-primary-700 font-medium">View All →</a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($featured_products as $product): ?>
                <div class="card card-hover cursor-pointer" onclick="window.location.href='product_detail.php?id=<?php echo $product['id']; ?>'">
                    <div class="aspect-square mb-3 overflow-hidden rounded-lg">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="font-semibold text-sm mb-1"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="text-neutral-500 text-xs mb-2"><?php echo htmlspecialchars($product['weight']); ?></p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-bold text-primary">₹<?php echo number_format($product['price'], 2); ?></span>
                            <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                            <span class="text-neutral-400 text-xs line-through block">₹<?php echo number_format($product['original_price'], 2); ?></span>
                            <?php endif; ?>
                        </div>
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

    <!-- Categories Section -->
    <section class="py-16 bg-surface">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-primary">Shop by Category</h2>
                <a href="product_categories.php" class="text-primary-600 hover:text-primary-700 font-medium">View All →</a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php foreach ($categories as $category): ?>
                <a href="product_categories.php?category=<?php echo urlencode($category['name']); ?>" class="card card-hover text-center">
                    <div class="aspect-square mb-3 overflow-hidden rounded-lg">
                        <img src="<?php echo htmlspecialchars($category['image_url']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="font-semibold text-sm"><?php echo htmlspecialchars($category['name']); ?></h3>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Trending Products Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-primary">Trending in Your Area</h2>
                <a href="product_categories.php" class="text-primary-600 hover:text-primary-700 font-medium">View All →</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($trending_products as $product): ?>
                <div class="card card-hover" onclick="window.location.href='product_detail.php?id=<?php echo $product['id']; ?>'">
                    <div class="relative">
                        <div class="aspect-w-4 aspect-h-3 mb-4 overflow-hidden rounded-lg">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover" />
                        </div>
                        <span class="absolute top-2 left-2 bg-accent text-white text-xs px-2 py-1 rounded-full">🔥 Trending</span>
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

    <?php else: ?>
    <!-- Search Results Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-primary mb-2">Search Results</h2>
                <p class="text-neutral-600">Found <?php echo count($products); ?> products for "<?php echo htmlspecialchars($search); ?>"</p>
            </div>
            
            <?php if (!empty($products)): ?>
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
            <?php else: ?>
            <div class="text-center py-16">
                <svg class="mx-auto h-24 w-24 text-neutral-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="text-xl font-bold text-neutral-800 mb-2">No products found</h3>
                <p class="text-neutral-600 mb-4">Try searching with different keywords</p>
                <a href="homepage.php" class="btn-primary">Browse All Products</a>
            </div>
            <?php endif; ?>
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
                        <span class="text-2xl font-bold">KITKIT Shopping</span>
                    </div>
                    <p class="text-neutral-300 mb-6 max-w-md">
                        Your trusted online grocery partner. Fresh products, best prices, delivered across India.
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="product_categories.php" class="text-neutral-300 hover:text-white transition-colors">Browse Categories</a></li>
                        <li><a href="order_tracking.php" class="text-neutral-300 hover:text-white transition-colors">Track Your Order</a></li>
                        <li><a href="user_profile_dashboard.php" class="text-neutral-300 hover:text-white transition-colors">My Account</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-neutral-300">
                        <li>📞 1800-KITKIT-SHOP</li>
                        <li>📧 support@kitkitshopping.com</li>
                        <li>📍 All India Delivery</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-neutral-700 mt-12 pt-8 text-center text-neutral-400">
                <p>&copy; 2025 <a href="https://gtai.in" target="_blank" class="text-primary hover:text-primary-300 transition-colors">GTAI.in</a> All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Help Button -->
    <a href="https://wa.me/919876543210?text=Hi%20KITKIT%20Shopping,%20I%20need%20help%20with%20my%20order" class="whatsapp-float" target="_blank" title="Chat with us on WhatsApp">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
        </svg>
    </a>

    <!-- Location Change Modal -->
    <div id="locationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-xl font-bold text-primary mb-4">Change Delivery Location</h3>
                <form method="POST">
                    <div class="form-group mb-4">
                        <label for="modal-pincode">Pincode</label>
                        <input type="text" id="modal-pincode" name="pincode" placeholder="Enter 6-digit pincode" required maxlength="6" pattern="[0-9]{6}" class="input-field">
                    </div>
                    <div class="form-group mb-4">
                        <label for="modal-city">City</label>
                        <select id="modal-city" name="city" required class="input-field">
                            <option value="">Choose your city</option>
                            <?php foreach ($cities as $city): ?>
                            <option value="<?php echo htmlspecialchars($city['city_name']); ?>" data-state="<?php echo htmlspecialchars($city['state_name']); ?>" data-pincode="<?php echo htmlspecialchars($city['pincode']); ?>">
                                <?php echo htmlspecialchars($city['city_name']); ?>, <?php echo htmlspecialchars($city['state_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="modal-state">State</label>
                        <input type="text" id="modal-state" name="state" readonly class="input-field">
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeLocationModal()" class="flex-1 bg-neutral-200 text-neutral-700 py-2 px-4 rounded-lg">Cancel</button>
                        <button type="submit" name="set_location" value="1" class="flex-1 btn-primary">Update Location</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
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
                    updateCartBadges(result.cart_count);
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

        function changeLocation() {
            document.getElementById('locationModal').classList.remove('hidden');
        }

        function closeLocationModal() {
            document.getElementById('locationModal').classList.add('hidden');
        }

        // Location modal city selection
        document.getElementById('modal-city').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const state = selectedOption.getAttribute('data-state');
            const pincode = selectedOption.getAttribute('data-pincode');
            
            document.getElementById('modal-state').value = state || '';
            document.getElementById('modal-pincode').value = pincode || '';
        });
    </script>
</body>
</html>