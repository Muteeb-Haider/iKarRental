<?php

session_start();
include 'storage.php';
$cars = (new JsonIO('cars.json'))->load();

// Apply filters if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passengers = isset($_POST['passengers']) ? (int) $_POST['passengers'] : 0;
    $transmission = $_POST['transmission'] ?? '';
    $minPrice = isset($_POST['min-price']) ? (int) $_POST['min-price'] : 0;
    $maxPrice = isset($_POST['max-price']) ? (int) $_POST['max-price'] : PHP_INT_MAX;
    $startDate = $_POST['start-date'] ?? '';
    $endDate = $_POST['end-date'] ?? '';

    $filteredCars = array_filter($cars, function ($car) use ($passengers, $transmission, $minPrice, $maxPrice, $startDate, $endDate) {
        // Filter by number of passengers
        $meetsPassengers = $passengers === null || $car['passengers'] >= $passengers;

        // Filter by transmission type
        $meetsTransmission = $transmission === null || $car['transmission'] === $transmission;

        // Filter by daily price range
        $meetsPrice = ($minPrice === null || $car['daily_price_huf'] >= $minPrice) &&
            ($maxPrice === null || $car['daily_price_huf'] <= $maxPrice);

        $meetsAvailability = true; 
        return $meetsPassengers && $meetsTransmission && $meetsPrice && $meetsAvailability;
    });
} else {
    $filteredCars = $cars;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>iKarRental</title>
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
    <?php if (!isset($_SESSION['user'])): ?>
        <div class="main">
            <h1>Rent cars easily!</h1>
            <a  href="register.php" class="registration-btn">Registration</a>

        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="filter-section">

            <span>seats</span>
            <button type="button" onclick="decrementPassengers()">-</button>
            <input id="passengers" name="passengers" min="0" type="number" value="<?= $_POST['passengers'] ?? 0 ?>" />
            <button type="button" onclick="incrementPassengers()">+</button>
            <span>from</span>
            <input id="start-date" name="start-date" type="date" value="<?= $_POST['start-date'] ?? '' ?>" />
            <span>until</span>
            <input id="end-date" name="end-date" type="date" value="<?= $_POST['end-date'] ?? '' ?>" />
            <select id="transmission" name="transmission">
                <option value="">Gear type</option>
                <option value="Automatic" <?= (isset($_POST['transmission']) && $_POST['transmission'] === 'Automatic') ? 'selected' : '' ?>>Automatic</option>
                <option value="Manual" <?= (isset($_POST['transmission']) && $_POST['transmission'] === 'Manual') ? 'selected' : '' ?>>Manual</option>
            </select>
            <input id="min-price" name="min-price" placeholder="14,000" type="number"
                value="<?= $_POST['min-price'] ?? '' ?>" />
            <span>-</span>
            <input id="max-price" name="max-price" placeholder="21,000" type="number"
                value="<?= $_POST['max-price'] ?? '' ?>" />
            <span> Ft</span>
            <button type=" submit">Filter</button>
        </div>
    </form>

    <div class="car-list">
        <?php foreach ($filteredCars as $car): ?>
            <div class="car-item">
                <img alt="<?= $car['brand'] . ' ' . $car['model'] ?>" height="200" src="<?= $car['image'] ?>" width="300" />
                <?php if (isset($_SESSION['user']) && isset($_SESSION['user']['email']) && $_SESSION['user']['email'] == "admin@ikarrental.hu"): ?>
                    <div class="action-buttons">
                        <a class="delete-btn" href="delete_car.php?id=<?= $car['id'] ?>">Delete</a>
                        <a class="edit-btn" href="edit_car.php?id=<?= $car['id'] ?>">Edit</a>
                    </div>
                <?php endif; ?>
                <div class="details">
                    <div class="price"><?= $car['daily_price_huf'] ?></div>
                    <div class="name"><?= $car['brand'] ?></div>
                    <div class="info"><?= $car['passengers'] ?> seats - <?= $car['transmission'] ?></div>
                    <a href="car_details.php?id=<?= $car['id'] ?>" class="book-btn">Book</a>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (isset($_SESSION['user']) && isset($_SESSION['user']['email']) && $_SESSION['user']['email'] == "admin@ikarrental.hu"): ?>
            <div class="car-item">
                <img src="plus.png" style="border-radius:50%; ">
                <div class="details">
                    <a href="add_car.php" class="book-btn" style="width:100%;">Add car</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function incrementPassengers() {
            const input = document.getElementById('passengers');
            input.value = parseInt(input.value) + 1;
        }

        function decrementPassengers() {
            const input = document.getElementById('passengers');
            if (input.value > 0) {
                input.value = parseInt(input.value) - 1;
            }
        }
    </script>
</body>

</html>