<?php
// Database configuration
$host = 'localhost';
$dbname = 'quickcart_pro';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get all products
function getProducts($pdo, $limit = null) {
    $sql = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to get product by ID
function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Function to get products by category
function getProductsByCategory($pdo, $category, $limit = null) {
    $sql = "SELECT * FROM products WHERE category = ? AND status = 'active' ORDER BY name ASC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$category]);
    return $stmt->fetchAll();
}

// Function to search products
function searchProducts($pdo, $query) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE (name LIKE ? OR description LIKE ?) AND status = 'active' ORDER BY name ASC");
    $searchTerm = "%$query%";
    $stmt->execute([$searchTerm, $searchTerm]);
    return $stmt->fetchAll();
}

// Function to add to cart
function addToCart($productId, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
    
    return true;
}

// Function to update cart quantity
function updateCartQuantity($productId, $quantity) {
    if (isset($_SESSION['cart'][$productId])) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }
    return true;
}

// Function to remove from cart
function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
    return true;
}

// Function to get cart items with product details
function getCartItems($pdo) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return [];
    }
    
    $productIds = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND status = 'active'");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll();
    
    $cartItems = [];
    foreach ($products as $product) {
        $product['cart_quantity'] = $_SESSION['cart'][$product['id']];
        $product['subtotal'] = $product['price'] * $product['cart_quantity'];
        $cartItems[] = $product;
    }
    
    return $cartItems;
}

// Function to get cart total
function getCartTotal($pdo) {
    $cartItems = getCartItems($pdo);
    $total = 0;
    
    foreach ($cartItems as $item) {
        $total += $item['subtotal'];
    }
    
    return $total;
}

// Function to clear cart
function clearCart() {
    $_SESSION['cart'] = [];
    return true;
}
?>