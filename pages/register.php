<?php
session_start();
include_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $address = trim($_POST['address']);
    $pincode = trim($_POST['pincode']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name) || empty($email) || empty($mobile) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $error = 'Please enter a valid 10-digit mobile number';
    } else {
        // Check if user already exists
        $existingUser = getUserByEmail($pdo, $email);
        if ($existingUser) {
            $error = 'An account with this email already exists';
        } else {
            // Create new user
            $userData = [
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'address' => $address,
                'pincode' => $pincode,
                'city' => $city,
                'state' => $state,
                'password' => $password
            ];
            
            if (createUser($pdo, $userData)) {
                $success = 'Account created successfully! Please login.';
            } else {
                $error = 'Error creating account. Please try again.';
            }
        }
    }
}

// Get Indian cities
$cities = getIndianCities($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - KITKIT Shopping</title>
    <link rel="stylesheet" href="../css/main.css" />
</head>
<body class="bg-gradient-to-br from-primary-50 to-secondary-50 min-h-screen flex items-center justify-center py-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-medium p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-primary mb-2">Create Account</h1>
            <p class="text-neutral-600">Join KITKIT Shopping for fresh groceries</p>
        </div>

        <?php if ($error): ?>
        <div class="bg-error-50 border border-error-200 text-error-600 px-4 py-3 rounded-lg mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-success-50 border border-success-200 text-success-600 px-4 py-3 rounded-lg mb-4">
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Full Name *</label>
                <input type="text" id="name" name="name" required class="input-field" placeholder="Enter your full name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-neutral-700 mb-2">Email Address *</label>
                <input type="email" id="email" name="email" required class="input-field" placeholder="Enter your email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div>
                <label for="mobile" class="block text-sm font-medium text-neutral-700 mb-2">Mobile Number *</label>
                <input type="tel" id="mobile" name="mobile" required class="input-field" placeholder="Enter 10-digit mobile number" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($_POST['mobile'] ?? ''); ?>">
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-neutral-700 mb-2">Address</label>
                <textarea id="address" name="address" class="input-field" rows="2" placeholder="Enter your address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="pincode" class="block text-sm font-medium text-neutral-700 mb-2">Pincode</label>
                    <input type="text" id="pincode" name="pincode" class="input-field" placeholder="6-digit pincode" maxlength="6" pattern="[0-9]{6}" value="<?php echo htmlspecialchars($_POST['pincode'] ?? ''); ?>">
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-neutral-700 mb-2">City</label>
                    <select id="city" name="city" class="input-field">
                        <option value="">Select City</option>
                        <?php foreach ($cities as $city): ?>
                        <option value="<?php echo htmlspecialchars($city['city_name']); ?>" data-state="<?php echo htmlspecialchars($city['state_name']); ?>" data-pincode="<?php echo htmlspecialchars($city['pincode']); ?>" <?php echo (($_POST['city'] ?? '') === $city['city_name']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($city['city_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="state" class="block text-sm font-medium text-neutral-700 mb-2">State</label>
                <input type="text" id="state" name="state" readonly class="input-field" value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">Password *</label>
                <input type="password" id="password" name="password" required class="input-field" placeholder="Minimum 6 characters">
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-neutral-700 mb-2">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="input-field" placeholder="Confirm your password">
            </div>

            <button type="submit" class="w-full btn-primary">Create Account</button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-neutral-600">Already have an account? <a href="login.php" class="text-primary hover:text-primary-600 font-medium">Sign in</a></p>
        </div>

        <div class="mt-4 text-center">
            <a href="homepage.php" class="text-neutral-500 hover:text-primary text-sm">← Back to Home</a>
        </div>
    </div>

    <script>
        // Auto-fill state when city is selected
        document.getElementById('city').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const state = selectedOption.getAttribute('data-state');
            const pincode = selectedOption.getAttribute('data-pincode');
            
            document.getElementById('state').value = state || '';
            if (pincode) {
                document.getElementById('pincode').value = pincode;
            }
        });
    </script>
</body>
</html>