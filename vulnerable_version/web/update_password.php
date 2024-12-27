<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
} catch (Exception $e) {
    die("Database connection error.");
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $username = $_SESSION['username'];

    // CSRF vulnerability (no token used), shown here but not patched
    $sql = "UPDATE users SET password = '$new_password' WHERE username = '$username'";
    $res = $conn->query($sql);

    if ($res) {
        $message = "<p>Password updated successfully.</p>";
    } else {
        $message = "<p>Error updating password.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Password</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin:0; padding:0; }
        header { background: #333; color: #fff; padding: 10px 20px; }
        header nav a { color: #fff; text-decoration: none; margin-right: 20px; }
        main { padding: 20px; max-width:400px; margin:40px auto; background:#fff; border:1px solid #ddd; }
        form label { display:block; margin-bottom:10px; }
        input[type="password"] {
            padding:6px; width:100%; margin-bottom:15px; border:1px solid #ccc;
        }
        input[type="submit"] {
            padding:10px; background:#333; color:#fff; border:none; cursor:pointer;
        }
        p { margin-top:15px; }
    </style>
</head>
<body>
<header>
    <nav>
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="update_password.php">Update Password</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main>
    <h1>Update Password</h1>
    <?php echo $message; ?>
    <form method="post" action="update_password.php">
        <label>New Password:</label>
        <input type="password" name="new_password" required>
        <input type="submit" value="Update Password">
    </form>
</main>
</body>
</html>
