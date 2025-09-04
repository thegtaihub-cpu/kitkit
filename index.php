<?php
session_start();
include_once 'config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get cart count
$cart_count = array_sum($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>QuickCart Pro - Your Ultimate Shopping Destination</title>
    <link rel="stylesheet" href="css/main.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .redirect-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 90%;
        }
        
        .logo {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 20px;
        }
        
        .loading-text {
            font-size: 1.2em;
            color: #333;
            margin-bottom: 30px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .redirect-info {
            font-size: 0.9em;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="redirect-container">
        <div class="logo">QuickCart Pro</div>
        <div class="loading-text">Welcome to your ultimate shopping destination!</div>
        <div class="spinner"></div>
        <div class="redirect-info">Redirecting you to the homepage...</div>
    </div>

    <script>
        // Show loading for 2 seconds then redirect to homepage
        setTimeout(function() {
            window.location.href = 'pages/homepage.php';
        }, 2000);
        
        // Fallback redirect in case of any issues
        window.addEventListener('load', function() {
            setTimeout(function() {
                if (window.location.pathname.endsWith('index.php') || window.location.pathname.endsWith('/')) {
                    window.location.href = 'pages/homepage.php';
                }
            }, 3000);
        });
    </script>
</body>
</html>