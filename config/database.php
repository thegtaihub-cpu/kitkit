<?php
// Database configuration for KITKIT Shopping
$host = 'localhost';
$dbname = 'kitkit_shopping';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get all products with search
function getProducts($pdo, $search = '', $category = '', $limit = null) {
    $sql = "SELECT * FROM products WHERE status = 'active'";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (name LIKE ? OR description LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($category)) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
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

// Function to get all categories
function getCategories($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC");
    $stmt->execute();
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

// Function to get user by email
function getUserByEmail($pdo, $email) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

// Function to create user
function createUser($pdo, $userData) {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, mobile, address, pincode, city, state, password_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    return $stmt->execute([
        $userData['name'],
        $userData['email'],
        $userData['mobile'],
        $userData['address'],
        $userData['pincode'],
        $userData['city'],
        $userData['state'],
        password_hash($userData['password'], PASSWORD_DEFAULT)
    ]);
}

// Function to verify user login
function verifyUser($pdo, $email, $password) {
    $user = getUserByEmail($pdo, $email);
    if ($user && password_verify($password, $user['password_hash'])) {
        return $user;
    }
    return false;
}

// Function to add to wishlist
function addToWishlist($pdo, $userId, $productId) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())");
    return $stmt->execute([$userId, $productId]);
}

// Function to get user wishlist
function getUserWishlist($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT p.* FROM products p JOIN wishlist w ON p.id = w.product_id WHERE w.user_id = ? AND p.status = 'active' ORDER BY w.created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Function to get Indian cities with pincodes
function getIndianCities($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM indian_cities ORDER BY city_name ASC");
    $stmt->execute();
    return $stmt->fetchAll();
}
?>