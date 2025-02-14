<?php
session_start();
include 'storage.php';
$cars = (new JsonIO('cars.json'))->load();
function getCarIndexById($cars, $id)
{
    foreach ($cars as $index => $car) {
        if ($car['id'] == $id) {
            return $index; // Return the index if the ID matches
        }
    }
    return -1; // Return -1 if no match is found
}
$carId = $_GET['id'];
$carIndex = getCarIndexById($cars, $carId);
?>


<html>

<head>
    <title>
        iKarRental
    </title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/car_details.css">
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
    <div class="container">
        <div class="card">
            <img alt="Red Honda Civic parked on a road with a clear sky in the background" height="300"
                src=<?= $cars[$carIndex]['image'] ?> width="450" />
            <div class="card-content">
                <h1>
                    <?= $cars[$carIndex]['brand'] ?>
                </h1>
                <div class="details">
                    <div>
                        <p>
                            Fuel: <?= $cars[$carIndex]['fuel_type'] ?>
                        </p>
                        <p>
                            Shifter: <?= $cars[$carIndex]['transmission'] ?>
                        </p>
                    </div>
                    <div>
                        <p>
                            Year of manufacture: <?= $cars[$carIndex]['year'] ?>
                        </p>
                        <p>
                            Number of seats: <?= $cars[$carIndex]['passengers'] ?>
                        </p>
                    </div>
                </div>
                <div class="price">
                    <?= $cars[$carIndex]['daily_price_huf'] ?>/day
                </div>
                <div class="actions">
                    <form method="post" action="booking_confirmation.php">
                        <input type="hidden" name="car_id" value="<?= $cars[$carIndex]['id'] ?>">
                        <input type="hidden" name="user_email" value="<?= $_SESSION['user']['email'] ?? '' ?>">
                        <div class="btn">
                            <button type="submit" class="book-it" name="book">Book it</button>

                            <label for="from">from</label>
                            <input class="dt" type="date" name="start_date" placeholder="Start Date" required>
                            <label for="t">to</label>

                            <input class="dt" type="date" name="end_date" placeholder="End Date" required>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</body>

</html>