<?php
session_start();
include_once 'config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get cart count
$cart_count = array_sum($_SESSION['cart']);

// Check if user is requesting location
if (isset($_POST['set_location'])) {
    $_SESSION['user_location'] = [
        'city' => $_POST['city'],
        'pincode' => $_POST['pincode'],
        'state' => $_POST['state']
    ];
    
    // If user is logged in, update their profile
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("UPDATE users SET city = ?, pincode = ?, state = ? WHERE id = ?");
        $stmt->execute([$_POST['city'], $_POST['pincode'], $_POST['state'], $_SESSION['user_id']]);
    }
    
    header('Location: pages/homepage.php');
    exit;
}

// Get Indian cities for location selection
$cities = getIndianCities($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KITKIT Shopping - Your Ultimate Shopping Destination</title>
    <link rel="stylesheet" href="css/main.css" />
    <meta name="description" content="KITKIT Shopping - Fresh groceries, fruits, vegetables delivered to your doorstep across India. Best prices, quality products.">
    <meta name="keywords" content="online grocery, fresh fruits, vegetables, dairy products, home delivery, India">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #2D5A27 0%, #F4A261 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .location-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
            backdrop-filter: blur(10px);
        }
        
        .logo {
            font-size: 3em;
            font-weight: bold;
            color: #2D5A27;
            margin-bottom: 10px;
        }
        
        .tagline {
            color: #F4A261;
            font-size: 1.1em;
            margin-bottom: 30px;
            font-weight: 500;
        }
        
        .location-form {
            text-align: left;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2D5A27;
        }
        
        .form-group select,
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #2D5A27;
        }
        
        .submit-btn {
            width: 100%;
            background: #2D5A27;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .submit-btn:hover {
            background: #244821;
        }
        
        .gps-btn {
            width: 100%;
            background: #F4A261;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }
        
        .gps-btn:hover {
            background: #E6944D;
        }
        
        .or-divider {
            text-align: center;
            margin: 20px 0;
            color: #6b7280;
            position: relative;
        }
        
        .or-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
            z-index: 1;
        }
        
        .or-divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>
    <div class="location-container">
        <div class="logo">KITKIT Shopping</div>
        <div class="tagline">Fresh groceries delivered to your doorstep</div>
        
        <button class="gps-btn" onclick="getLocation()">
            📍 Use My Current Location
        </button>
        
        <div class="or-divider">
            <span>OR</span>
        </div>
        
        <form class="location-form" method="POST">
            <div class="form-group">
                <label for="pincode">Enter Your Pincode</label>
                <input type="text" id="pincode" name="pincode" placeholder="e.g., 400001" required maxlength="6" pattern="[0-9]{6}">
            </div>
            
            <div class="form-group">
                <label for="city">Select Your City</label>
                <select id="city" name="city" required>
                    <option value="">Choose your city</option>
                    <?php foreach ($cities as $city): ?>
                    <option value="<?php echo htmlspecialchars($city['city_name']); ?>" data-state="<?php echo htmlspecialchars($city['state_name']); ?>" data-pincode="<?php echo htmlspecialchars($city['pincode']); ?>">
                        <?php echo htmlspecialchars($city['city_name']); ?>, <?php echo htmlspecialchars($city['state_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state" readonly>
            </div>
            
            <input type="hidden" name="set_location" value="1">
            <button type="submit" class="submit-btn">Continue Shopping</button>
        </form>
    </div>

    <script>
        // Auto-fill state and pincode when city is selected
        document.getElementById('city').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const state = selectedOption.getAttribute('data-state');
            const pincode = selectedOption.getAttribute('data-pincode');
            
            document.getElementById('state').value = state || '';
            document.getElementById('pincode').value = pincode || '';
        });
        
        // Auto-fill city when pincode is entered
        document.getElementById('pincode').addEventListener('input', function() {
            const pincode = this.value;
            if (pincode.length === 6) {
                const citySelect = document.getElementById('city');
                const options = citySelect.options;
                
                for (let i = 0; i < options.length; i++) {
                    if (options[i].getAttribute('data-pincode') === pincode) {
                        citySelect.selectedIndex = i;
                        document.getElementById('state').value = options[i].getAttribute('data-state');
                        break;
                    }
                }
            }
        });
        
        // GPS Location function
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Use reverse geocoding to get location details
                    fetch(`https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lng}&key=YOUR_API_KEY`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.results && data.results[0]) {
                                const result = data.results[0];
                                const city = result.components.city || result.components.town || result.components.village;
                                const state = result.components.state;
                                const pincode = result.components.postcode;
                                
                                if (city && state && pincode) {
                                    document.getElementById('city').value = city;
                                    document.getElementById('state').value = state;
                                    document.getElementById('pincode').value = pincode;
                                }
                            }
                        })
                        .catch(error => {
                            alert('Unable to get location details. Please enter manually.');
                        });
                }, function(error) {
                    alert('Location access denied. Please enter your location manually.');
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }
    </script>
</body>
</html>