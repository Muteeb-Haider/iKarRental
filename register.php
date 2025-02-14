<?php

session_start();
include 'storage.php';
$jsonIO = new JsonIO('users.json');
$storage = new Storage($jsonIO);

// Initialize the $errors array
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $passwordAgain = trim($_POST['password-again']);

    // Validation
    if (empty($fullname)) {
        $errors[] = 'Full Name is required.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($password) || strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if (empty($passwordAgain) || $passwordAgain !== $password) {
        $errors[] = 'Passwords do not match.';
    }

    // Check if the email already exists
    $users = $storage->findAll();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $errors[] = 'Email address already registered.';
            break;
        }
    }
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Add the new user
        $storage->add([
            'fullname' => $fullname,
            'email' => $email,
            'password' => $hashedPassword,
            'is_admin' => false
        ]);

        // Redirect to login page
        header('Location: login.php');
        exit;
    }
}
?>



<html>

<head>
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/register.css">

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
    </div>
    <div class="container">
        <h1>Registration</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo $_POST['fullname'] ?? ''; ?>">
                <?php if (isset($errors) && in_array('Full Name is required.', $errors)): ?>
                    <span class="error ">Full Name is required.</span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>">
                <?php if (isset($errors) && in_array('Please enter a valid email address.', $errors)): ?>
                    <span class="error">Please enter a valid email address.</span>
                <?php endif; ?>
                <?php if (isset($errors) && in_array('Email address already registered.', $errors)): ?>
                    <span class="error">Email address already registered.</span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <?php if (isset($errors) && in_array('Password must be at least 8 characters long.', $errors)): ?>
                    <span class="error">Password must be at least 8 characters long.</span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password-again">Confirm Password</label>
                <input type="password" id="password-again" name="password-again">
                <?php if (isset($errors) && in_array('Passwords do not match.', $errors)): ?>
                    <span class="error">Passwords do not match.</span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <input type="submit" value="Register">
            </div>
        </form>
    </div>
</body>

</html>