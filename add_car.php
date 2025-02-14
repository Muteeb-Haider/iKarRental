<?php
include 'storage.php';
session_start();

// Initialize variables for form data and error messages
$brand = $model = $year = $transmission = $fuel_type = $passengers = $daily_price_huf = $image = '';
$errors = [];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $brand = trim($_POST['brand'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $transmission = trim($_POST['transmission'] ?? '');
    $fuel_type = trim($_POST['fuel_type'] ?? '');
    $passengers = trim($_POST['passengers'] ?? '');
    $daily_price_huf = trim($_POST['daily_price_huf'] ?? '');
    $image = trim($_POST['image'] ?? '');

    // Validate brand
    if (empty($brand)) {
        $errors['brand'] = 'Brand is required.';
    }

    // Validate model
    if (empty($model)) {
        $errors['model'] = 'Model is required.';
    }

    // Validate year
    if (empty($year) || !is_numeric($year) || $year < 1900 || $year > date('Y')) {
        $errors['year'] = 'Invalid year.';
    }

    // Validate transmission
    if (empty($transmission) || !in_array($transmission, ['Automatic', 'Manual'])) {
        $errors['transmission'] = 'Invalid transmission type.';
    }

    // Validate fuel type
    if (empty($fuel_type) || !in_array($fuel_type, ['Petrol', 'Diesel', 'Electric'])) {
        $errors['fuel_type'] = 'Invalid fuel type.';
    }

    // Validate passengers
    if (empty($passengers) || !is_numeric($passengers) || $passengers < 1) {
        $errors['passengers'] = 'Invalid number of passengers.';
    }

    // Validate daily price
    if (empty($daily_price_huf) || !is_numeric($daily_price_huf) || $daily_price_huf < 0) {
        $errors['daily_price_huf'] = 'Invalid daily price.';
    }

    // Validate image URL
    // if (empty($image) || !filter_var($image, FILTER_VALIDATE_URL)) {
    //     $errors['image'] = 'Invalid image URL.';
    // }

    // If no errors, save the car data
    if (empty($errors)) {
        // Load existing cars
        $cars = (new JsonIO('cars.json'))->load();

        // Generate a unique ID for the new car
        $id = uniqid();

        // Add the new car to the array
        $cars[] = [
            'id' => $id,
            'brand' => $brand,
            'model' => $model,
            'year' => (int) $year,
            'transmission' => $transmission,
            'fuel_type' => $fuel_type,
            'passengers' => (int) $passengers,
            'daily_price_huf' => (int) $daily_price_huf,
            'image' => $image,
        ];

        // Save the updated cars array back to the JSON file
        (new JsonIO('cars.json'))->save($cars);

        // Redirect to the homepage or a success page
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add New Car - iKarRental</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
</head>

<body>
    <div class="header">
        <div class="logo">iKarRental</div>
        <div class="nav">

            <?php if (isset($_SESSION['user'])): ?>
                <a href="profile.php"><img src="pfp.png">Profile</a>
                <a class="registration" href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a class="registration" href="register.php">Register</a>
            <?php endif; ?>

        </div>
    </div>
    <div class="add-car-container">
        <h1>Add New Car</h1>
        <form method="POST" action="">
            <div class = "form-group">
                <label for="brand">Brand:</label>
                <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($brand) ?>" />
                <span class="error"><?= $errors['brand'] ?? '' ?></span>
            </div>
            <div class = "form-group">
                <label for="model">Model:</label>
                <input type="text" id="model" name="model" value="<?= htmlspecialchars($model) ?>" />
                <span class="error"><?= $errors['model'] ?? '' ?></span>
            </div>
            <div class = "form-group">
                <label for="year">Year:</label>
                <input type="number" id="year" name="year" value="<?= htmlspecialchars($year) ?>" />
                <span class="error"><?= $errors['year'] ?? '' ?></span>
            </div>
            <div class = "form-group">
                <label for="transmission">Transmission:</label>
                <select id="transmission" name="transmission">
                    <option value="">Select Transmission</option>
                    <option value="Automatic" <?= $transmission === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                    <option value="Manual" <?= $transmission === 'Manual' ? 'selected' : '' ?>>Manual</option>
                </select>
                <span class="error"><?= $errors['transmission'] ?? '' ?></span>
            </div>
            <div class = "form-group">
                <label for="fuel_type">Fuel Type:</label>
                <select id="fuel_type" name="fuel_type">
                    <option value="">Select Fuel Type</option>
                    <option value="Petrol" <?= $fuel_type === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                    <option value="Diesel" <?= $fuel_type === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                    <option value="Electric" <?= $fuel_type === 'Electric' ? 'selected' : '' ?>>Electric</option>
                </select>
                <span class="error"><?= $errors['fuel_type'] ?? '' ?></span>
            </div>
            <div class = "form-group">
                <label for="passengers">Passengers:</label>
                <input type="number" id="passengers" name="passengers" value="<?= htmlspecialchars($passengers) ?>" />
                <span class="error"><?= $errors['passengers'] ?? '' ?></span>
            </div>
            <div class = "form-group">
                <label for="daily_price_huf">Daily Price (HUF):</label>
                <input type="number" id="daily_price_huf" name="daily_price_huf"
                    value="<?= htmlspecialchars($daily_price_huf) ?>" />
                <span class="error"><?= $errors['daily_price_huf'] ?? '' ?></span>
            </div>
            <div class = "form-group">
                <label for="image">Image URL:</label>
                <input type="text" id="image" name="image" value="<?= htmlspecialchars($image) ?>" />
                <span class="error"><?= $errors['image'] ?? '' ?></span>
            </div>
            <button type="submit">Add Car</button>
        </form>
    </div>
</body>

</html>