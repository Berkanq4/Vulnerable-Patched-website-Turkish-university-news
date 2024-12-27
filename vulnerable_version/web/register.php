<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Blind OS Command Injection vulnerability
    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'user')";
    $res = $conn->query($sql);

    if ($res) {
        // Blind OS Command Injection #2
        $checkCmd = "nslookup " . $username; // vulnerable
        exec($checkCmd, $output, $return_var);

        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $message = "<p>Registration failed. Possibly user already exists.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        header { background: #333; color: #fff; padding: 10px 20px; }
        header nav a { color: #fff; text-decoration: none; margin-right: 20px; }
        main { padding: 20px; max-width: 400px; margin: 40px auto; background: #fff; border: 1px solid #ddd; }
        form label { display: block; margin-bottom: 10px; }
        input[type="text"], input[type="password"] {
            padding: 6px; width: 100%; margin-bottom: 15px; border: 1px solid #ccc;
        }
        input[type="submit"] {
            padding: 10px; background: #333; color: #fff; border: none; cursor: pointer;
        }
        p { margin-top: 15px; }
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
    <h1>Register</h1>
    <?php echo $message; ?>
    <form method="post" action="register.php">
        <label>Username (as email):</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <input type="submit" value="Register">
    </form>
</main>
</body>
</html>