<?php

session_start(); // Start the session


include 'storage.php';

$jsonIO = new JsonIO('users.json');

$storage = new Storage($jsonIO);

if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $users = $storage->findAll(['email' => $email]);

    if (!empty($users)) {
        $user = reset($users); // Use reset() to get the first element of the array
        if (isset($user['password']) && password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user'] = [
                'email' => $user['email'],
                'fullname' => $user['fullname']
            ];

            // Redirect to profile page
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<html>

<head>
    <title>iKarRental - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="header">
        <div class="logo">iKarRental</div>
        <div class="nav">
            <?php if (isset($_SESSION['user'])): ?>
               
                <a href="profile.php">Profile</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a class="registration" href="register.php">Registration</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="login-container">
        <h1>Login</h1>
        <form method="post" action="">
            <input type="email" placeholder="Email address" name="email" value="<?php echo $_POST['email'] ?? ''; ?>"
                required>
            <input type="password" placeholder="Password" name="password" required>
            <button type="submit">Login</button>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
        </form>
    </div>
</body>

</html>