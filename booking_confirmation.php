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

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$carId = $_POST['car_id'];
$userEmail = $_POST['user_email'];
$startDate = $_POST['start_date'];
$endDate = $_POST['end_date'];
$carIndex = getCarIndexById($cars, $carId);

// Check for overlapping bookings
$bookings = (new JsonIO('bookings.json'))->load();
$overlap = false;

foreach ($bookings as $booking) {
    if (
        $booking['car_id'] == $carId &&
        (($startDate >= $booking['start_date'] && $startDate <= $booking['end_date']) ||
            ($endDate >= $booking['start_date'] && $endDate <= $booking['end_date']))
    ) {
        $overlap = true;
        break;
    }
}

if ($overlap) {
    $error = "The car is already booked for the selected period.";
} else {

    $newBooking = [
        'car_id' => $carId,
        'user_email' => $userEmail,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ];
    $bookings[] = $newBooking;
    (new JsonIO('bookings.json'))->save($bookings);

    $success = "Booking successful! Total price: ";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Booking Confirmation</title>
    <link href="styles.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/booking_confirmation.css">
    <style>

    </style>
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
        <?php if (isset($error)): ?>
            <div class="content">
                <div class="icon_w">
                    <i class="fas fa-times-circle">
                    </i>
                </div>
                <h1>
                    Booking failed!
                </h1>
                <p>
                    The <?= $cars[$carId]['brand'] . " " . $cars[$carId]['model'] ?> is not available in the specified interval
                    from <?= $startDate . "–" . $endDate ?>.
                    <br />
                    Try entering a different interval or search for another vehicle.
                </p>
                <a href="car_details.php?id=<?= $carId ?>" class="btn">
                    Back to the vehicle side
                </a>
            </div>
        <?php elseif (isset($success)): ?>
            <div class="content">
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1>Successful booking!</h1>
                <p>
                    The <?= $cars[$carId]['brand'] . " " . $cars[$carId]['model'] ?> has been successfully booked for the
                    interval <?= $startDate . "–" . $endDate ?>
                    <br />
                    You can track the status of your reservation on your profile page.
                </p>
                <a href="profile.php" class="btn">My profile</a>
            </div>

        <?php endif; ?>
    </div>
</body>

</html>