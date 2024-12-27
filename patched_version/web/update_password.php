<?php
session_start();


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
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Database connection error.");
}

$message = "";

// Patched: CSRF is patched by creating another token just for the sake of patching this csrf vulnerability. Unique token for each session.
// This way the atacker will not be able to forge http request since this token is not known by him
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Checking CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "<p>CSRF token mismatch. Request blocked!</p>";
    } else {
        $new_password = trim($_POST['new_password']);
        $username = $_SESSION['username'];

        // Patched: Used prepare to protect against SQL injections
        $sql = "UPDATE users SET password = :newpwd WHERE username = :uname";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':newpwd' => $new_password,
            ':uname'  => $username
        ]);

        if ($stmt->rowCount() > 0) {
            $message = "<p>Password updated successfully.</p>";
        } else {
            $message = "<p>Error updating password or no change detected.</p>";
        }
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
        <!-- PATCHED: CSRF token included -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="submit" value="Update Password">
    </form>
</main>
</body>
</html>
