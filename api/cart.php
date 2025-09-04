<?php
session_start();
include_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'add':
        $productId = intval($input['product_id'] ?? 0);
        $quantity = intval($input['quantity'] ?? 1);
        
        if ($productId <= 0 || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID or quantity']);
            exit;
        }
        
        // Check if product exists
        $product = getProductById($pdo, $productId);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        // Add to cart
        addToCart($productId, $quantity);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Product added to cart',
            'cart_count' => array_sum($_SESSION['cart'])
        ]);
        break;
        
    case 'update':
        $productId = intval($input['product_id'] ?? 0);
        $quantity = intval($input['quantity'] ?? 0);
        
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            exit;
        }
        
        updateCartQuantity($productId, $quantity);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Cart updated',
            'cart_count' => array_sum($_SESSION['cart']),
            'cart_total' => number_format(getCartTotal($pdo), 2)
        ]);
        break;
        
    case 'remove':
        $productId = intval($input['product_id'] ?? 0);
        
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            exit;
        }
        
        removeFromCart($productId);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Product removed from cart',
            'cart_count' => array_sum($_SESSION['cart']),
            'cart_total' => number_format(getCartTotal($pdo), 2)
        ]);
        break;
        
    case 'clear':
        clearCart();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Cart cleared',
            'cart_count' => 0,
            'cart_total' => '0.00'
        ]);
        break;
        
    case 'get':
        $cartItems = getCartItems($pdo);
        $cartTotal = getCartTotal($pdo);
        
        echo json_encode([
            'success' => true,
            'cart_items' => $cartItems,
            'cart_count' => array_sum($_SESSION['cart']),
            'cart_total' => number_format($cartTotal, 2)
        ]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>