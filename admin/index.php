<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

include_once '../config/database.php';

// Get dashboard statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_products FROM products WHERE status = 'active'");
$stmt->execute();
$total_products = $stmt->fetch()['total_products'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users WHERE status = 'active'");
$stmt->execute();
$total_users = $stmt->fetch()['total_users'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM orders");
$stmt->execute();
$total_orders = $stmt->fetch()['total_orders'];

$stmt = $pdo->prepare("SELECT SUM(total_amount) as total_revenue FROM orders WHERE payment_status = 'paid'");
$stmt->execute();
$total_revenue = $stmt->fetch()['total_revenue'] ?? 0;

// Today's statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as today_orders FROM orders WHERE DATE(created_at) = CURDATE()");
$stmt->execute();
$today_orders = $stmt->fetch()['today_orders'];

$stmt = $pdo->prepare("SELECT SUM(total_amount) as today_revenue FROM orders WHERE DATE(created_at) = CURDATE() AND payment_status = 'paid'");
$stmt->execute();
$today_revenue = $stmt->fetch()['today_revenue'] ?? 0;

// Weekly statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as week_orders FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmt->execute();
$week_orders = $stmt->fetch()['week_orders'];

$stmt = $pdo->prepare("SELECT SUM(total_amount) as week_revenue FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND payment_status = 'paid'");
$stmt->execute();
$week_revenue = $stmt->fetch()['week_revenue'] ?? 0;

// Monthly statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as month_orders FROM orders WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
$stmt->execute();
$month_orders = $stmt->fetch()['month_orders'];

$stmt = $pdo->prepare("SELECT SUM(total_amount) as month_revenue FROM orders WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) AND payment_status = 'paid'");
$stmt->execute();
$month_revenue = $stmt->fetch()['month_revenue'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KITKIT Shopping</title>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .admin-sidebar {
            background: linear-gradient(135deg, #2D5A27 0%, #244821 100%);
            min-height: 100vh;
        }
        .stat-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-left: 4px solid;
        }
        .stat-card.primary { border-left-color: #2D5A27; }
        .stat-card.secondary { border-left-color: #F4A261; }
        .stat-card.success { border-left-color: #27AE60; }
        .stat-card.accent { border-left-color: #E76F51; }
    </style>
</head>
<body class="bg-neutral-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="admin-sidebar w-64 p-6">
            <div class="text-white mb-8">
                <h1 class="text-2xl font-bold">KITKIT Admin</h1>
                <p class="text-primary-200">Dashboard</p>
            </div>
            
            <nav class="space-y-2">
                <a href="index.php" class="block px-4 py-2 text-white bg-primary-600 rounded-lg">Dashboard</a>
                <a href="products.php" class="block px-4 py-2 text-primary-200 hover:text-white hover:bg-primary-600 rounded-lg transition-colors">Products</a>
                <a href="categories.php" class="block px-4 py-2 text-primary-200 hover:text-white hover:bg-primary-600 rounded-lg transition-colors">Categories</a>
                <a href="orders.php" class="block px-4 py-2 text-primary-200 hover:text-white hover:bg-primary-600 rounded-lg transition-colors">Orders</a>
                <a href="users.php" class="block px-4 py-2 text-primary-200 hover:text-white hover:bg-primary-600 rounded-lg transition-colors">Users</a>
                <a href="reviews.php" class="block px-4 py-2 text-primary-200 hover:text-white hover:bg-primary-600 rounded-lg transition-colors">Reviews</a>
                <a href="banners.php" class="block px-4 py-2 text-primary-200 hover:text-white hover:bg-primary-600 rounded-lg transition-colors">Banners</a>
                <a href="settings.php" class="block px-4 py-2 text-primary-200 hover:text-white hover:bg-primary-600 rounded-lg transition-colors">Settings</a>
                <a href="export.php" class="block px-4 py-2 text-primary-200 hover:text-white hover:bg-primary-600 rounded-lg transition-colors">Export Data</a>
                <a href="logout.php" class="block px-4 py-2 text-primary-200 hover:text-white hover:bg-primary-600 rounded-lg transition-colors">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-primary">Dashboard Overview</h2>
                <p class="text-neutral-600">Welcome back, Admin!</p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card primary p-6 rounded-lg shadow-soft">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-neutral-600 text-sm">Total Products</p>
                            <p class="text-2xl font-bold text-primary"><?php echo $total_products; ?></p>
                        </div>
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                </div>

                <div class="stat-card secondary p-6 rounded-lg shadow-soft">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-neutral-600 text-sm">Total Users</p>
                            <p class="text-2xl font-bold text-secondary"><?php echo $total_users; ?></p>
                        </div>
                        <svg class="w-8 h-8 text-secondary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>

                <div class="stat-card success p-6 rounded-lg shadow-soft">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-neutral-600 text-sm">Total Orders</p>
                            <p class="text-2xl font-bold text-success"><?php echo $total_orders; ?></p>
                        </div>
                        <svg class="w-8 h-8 text-success" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
                        </svg>
                    </div>
                </div>

                <div class="stat-card accent p-6 rounded-lg shadow-soft">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-neutral-600 text-sm">Total Revenue</p>
                            <p class="text-2xl font-bold text-accent">₹<?php echo number_format($total_revenue, 2); ?></p>
                        </div>
                        <svg class="w-8 h-8 text-accent" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Today's Business -->
                <div class="card">
                    <h3 class="text-xl font-bold text-primary mb-4">Today's Business</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-600">Orders</span>
                            <span class="font-bold text-primary"><?php echo $today_orders; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-600">Revenue</span>
                            <span class="font-bold text-success">₹<?php echo number_format($today_revenue, 2); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Weekly Business -->
                <div class="card">
                    <h3 class="text-xl font-bold text-primary mb-4">This Week</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-600">Orders</span>
                            <span class="font-bold text-primary"><?php echo $week_orders; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-600">Revenue</span>
                            <span class="font-bold text-success">₹<?php echo number_format($week_revenue, 2); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Monthly Business -->
                <div class="card">
                    <h3 class="text-xl font-bold text-primary mb-4">This Month</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-600">Orders</span>
                            <span class="font-bold text-primary"><?php echo $month_orders; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-600">Revenue</span>
                            <span class="font-bold text-success">₹<?php echo number_format($month_revenue, 2); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <h3 class="text-xl font-bold text-primary mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="products.php?action=add" class="block w-full btn-primary text-center">Add New Product</a>
                        <a href="orders.php" class="block w-full btn-secondary text-center">Manage Orders</a>
                        <a href="export.php" class="block w-full btn-accent text-center">Export Data</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>