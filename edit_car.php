<?php
session_start();

f (!isset($_SESSION['user']) || $_SESSION['user']['email'] !== 'admin@ikarrental.hu') {
    header('Location: login.php');
    exit();
}

include 'storage.php';

$carId = $_GET['id'] ?? null;

if (!$carId) {
    header('Location: index.php');
    exit();
}

$cars = (new JsonIO('cars.json'))->load();
$bookings = (new JsonIO('bookings.json'))->load();

$car = null;
foreach ($cars as $c) {
    if ($c['id'] == $carId) {
        $car = $c;
        break;
    }
}

if (!$car) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car['brand'] = $_POST['brand'] ?? $car['brand'];
    $car['model'] = $_POST['model'] ?? $car['model'];
    $car['passengers'] = (int) ($_POST['passengers'] ?? $car['passengers']);
    $car['transmission'] = $_POST['transmission'] ?? $car['transmission'];
    $car['daily_price_huf'] = (int) ($_POST['daily_price_huf'] ?? $car['daily_price_huf']);
    $car['image'] = $_POST['image'] ?? $car['image'];

    foreach ($cars as &$c) {
        if ($c['id'] == $carId) {
            $c = $car;
            break;
        }
    }

    (new JsonIO('cars.json'))->save($cars);

    $bookings = array_filter($bookings, function ($booking) use ($carId) {
        return $booking['car_id'] != $carId;
    });

    (new JsonIO('bookings.json'))->save(array_values($bookings));

    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Car - iKarRental</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css 2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
</head>

<body>
    <div class="header">
        <div class="logo">iKarRental</div>
        <div class="nav">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="profile.php"><img src="pfp.png" alt="Profile" />Profile</a>
                <a class="registration" href="logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="edit-car-form">
        <h1>Edit Car</h1>
        <form method="POST" action="">
          
            <div class="form-group">
                <label for="brand">Brand:</label>
                <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($car['brand']) ?>" required>
            </div>

            <div class="form-group">
                <label for="model">Model:</label>
                <input type="text" id="model" name="model" value="<?= htmlspecialchars($car['model']) ?>" required>
            </div>

            <div class="form-group">
                <label for="passengers">Passengers:</label>
                <input type="number" id="passengers" name="passengers" value="<?= htmlspecialchars($car['passengers']) ?>" required>
            </div>

            <div class="form-group">
                <label for="transmission">Transmission:</label>
                <select id="transmission" name="transmission" required>
                    <option value="manual" <?= $car['transmission'] === 'manual' ? 'selected' : '' ?>>Manual</option>
                    <option value="automatic" <?= $car['transmission'] === 'automatic' ? 'selected' : '' ?>>Automatic</option>
                </select>
            </div>

            <div class="form-group">
                <label for="daily_price_huf">Daily Price (HUF):</label>
                <input type="number" id="daily_price_huf" name="daily_price_huf" value="<?= htmlspecialchars($car['daily_price_huf']) ?>" required>
            </div>

            <div class="form-group">
                <label for="image">Image URL:</label>
                <input type="url" id="image" name="image" value="<?= htmlspecialchars($car['image']) ?>" required>
            </div>

            <button type="submit">Update Car</button>
        </form>
    </div>
</body>

</html>