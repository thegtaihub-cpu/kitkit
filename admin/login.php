<?php
session_start();
include_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'] ?? '';
    $emergency_login = isset($_POST['emergency_login']);
    
    if (empty($username)) {
        $error = 'Please enter username';
    } else {
        // Check admin credentials
        if ($emergency_login) {
            // Emergency login - only username required
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND emergency_access = 1 AND status = 'active'");
            $stmt->execute([$username]);
        } else {
            // Regular login
            if (empty($password)) {
                $error = 'Please enter password';
            } else {
                $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND status = 'active'");
                $stmt->execute([$username]);
            }
        }
        
        if (!$error) {
            $admin = $stmt->fetch();
            
            if ($admin && ($emergency_login || password_verify($password, $admin['password_hash']))) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                // Update last login
                $stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$admin['id']]);
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid credentials';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - KITKIT Shopping</title>
    <link rel="stylesheet" href="../css/main.css">
</head>
<body class="bg-gradient-to-br from-primary-50 to-secondary-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-medium p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-primary mb-2">Admin Login</h1>
            <p class="text-neutral-600">KITKIT Shopping Administration</p>
        </div>

        <?php if ($error): ?>
        <div class="bg-error-50 border border-error-200 text-error-600 px-4 py-3 rounded-lg mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <!-- Regular Login Form -->
        <form method="POST" class="space-y-6" id="regularForm">
            <div>
                <label for="username" class="block text-sm font-medium text-neutral-700 mb-2">Username</label>
                <input type="text" id="username" name="username" required class="input-field" placeholder="Enter admin username">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">Password</label>
                <input type="password" id="password" name="password" required class="input-field" placeholder="Enter admin password">
            </div>

            <button type="submit" class="w-full btn-primary">Login</button>
        </form>

        <!-- Emergency Login Form -->
        <div class="mt-6 pt-6 border-t border-neutral-200">
            <form method="POST" class="space-y-4" id="emergencyForm">
                <div>
                    <label for="emergency_username" class="block text-sm font-medium text-error-600 mb-2">Emergency Access</label>
                    <input type="text" id="emergency_username" name="username" class="input-field border-error-300" placeholder="Emergency username only">
                </div>
                <input type="hidden" name="emergency_login" value="1">
                <button type="submit" class="w-full bg-error text-white py-3 px-6 rounded-lg font-semibold hover:bg-error-600 transition-colors">Emergency Login</button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <a href="../pages/homepage.php" class="text-neutral-500 hover:text-primary text-sm">← Back to Website</a>
        </div>
    </div>
</body>
</html>