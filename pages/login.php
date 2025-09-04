<?php
session_start();
include_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $user = verifyUser($pdo, $email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            // Update user location in session
            $_SESSION['user_location'] = [
                'city' => $user['city'],
                'pincode' => $user['pincode'],
                'state' => $user['state']
            ];
            
            header('Location: homepage.php');
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - KITKIT Shopping</title>
    <link rel="stylesheet" href="../css/main.css" />
</head>
<body class="bg-gradient-to-br from-primary-50 to-secondary-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-medium p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-primary mb-2">Welcome Back</h1>
            <p class="text-neutral-600">Sign in to your KITKIT Shopping account</p>
        </div>

        <?php if ($error): ?>
        <div class="bg-error-50 border border-error-200 text-error-600 px-4 py-3 rounded-lg mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-neutral-700 mb-2">Email Address</label>
                <input type="email" id="email" name="email" required class="input-field" placeholder="Enter your email">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">Password</label>
                <input type="password" id="password" name="password" required class="input-field" placeholder="Enter your password">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" class="rounded border-neutral-300 text-primary focus:ring-primary">
                    <span class="ml-2 text-sm text-neutral-600">Remember me</span>
                </label>
                <a href="forgot_password.php" class="text-sm text-primary hover:text-primary-600">Forgot password?</a>
            </div>

            <button type="submit" class="w-full btn-primary">Sign In</button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-neutral-600">Don't have an account? <a href="register.php" class="text-primary hover:text-primary-600 font-medium">Sign up</a></p>
        </div>

        <div class="mt-4 text-center">
            <a href="homepage.php" class="text-neutral-500 hover:text-primary text-sm">← Back to Home</a>
        </div>
    </div>
</body>
</html>