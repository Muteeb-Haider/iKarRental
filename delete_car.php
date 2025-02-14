<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['email'] !== 'admin@ikarrental.hu') {
    header('Location: login.php');
    exit();
}

include 'storage.php';


$carId = $_GET['id'] ?? null;

if ($carId) {
    $cars = (new JsonIO('cars.json'))->load();

    $cars = array_filter($cars, function ($car) use ($carId) {
        return $car['id'] != $carId;
    });

    (new JsonIO('cars.json'))->save(array_values($cars)); // Re-index the array
}

header('Location: index.php');
exit();
?>