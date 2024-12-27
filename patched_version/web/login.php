<?php
session_start();

$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // PATCHED
} catch (Exception $e) {
    die("Database connection error.");
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Patched: Used "prepare" in order to protects against SQL injections
    $sql = "SELECT * FROM users WHERE username = :uname AND password = :pwd";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':uname' => $username,
        ':pwd'   => $password
    ]);

    // Patched: Removed unneccessary "exec" command which was causing the Blind OS injection
    // this is removed with safe logging if needed:
    // $logCmd = "ping -c 1 " . $username; 
    // exec($logCmd, $output, $return_var);
    

    if ($stmt->rowCount() > 0) {
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $message = "<p>Invalid login</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
    <h1>Login</h1>
    <?php echo $message; ?>
    <form method="post" action="login.php">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <input type="submit" value="Login">
    </form>
</main>
</body>
</html>
