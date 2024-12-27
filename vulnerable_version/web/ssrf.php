<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple navigation check
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// If the user submitted a URL, process it
$blacklist = ['localhost', '127.0.0.1', 'internal', 'https://en.wikipedia.org/wiki/Penguin'];
$result = "";

if (isset($_GET['target_url'])) {
    $url = $_GET['target_url'];
    // Simple blacklist-based filter
    foreach ($blacklist as $blocked) {
        if (stripos($url, $blocked) !== false) {
            $result = "URL not allowed!";
            break;
        }
    }
    // If not blocked, attempt to fetch
    if (!$result) {
        $contents = @file_get_contents($url);
        if ($contents === false) {
            $result = "Could not fetch content.";
        } else {
            $result = htmlspecialchars($contents);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SSRF Demo</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        header { background: #333; color: #fff; padding: 10px 20px; }
        header nav a { color: #fff; text-decoration: none; margin-right: 20px; }
        main {
            padding: 20px; max-width: 600px; margin: 40px auto; background: #fff;
            border: 1px solid #ddd;
        }
        form label { display: block; margin-bottom: 5px; }
        input[type="text"] {
            padding: 6px; width: 100%; margin-bottom: 15px; border: 1px solid #ccc;
        }
        input[type="submit"] {
            padding: 10px; background: #333; color: #fff; border: none; cursor: pointer;
        }
        .result {
            margin-top: 20px; background: #eee; padding: 10px; word-wrap: break-word;
        }
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
    <h1>Enter University News URL to Fetch its Data</h1>
    <p>Below you can give us a university news website url. We will provide you the fetched content of the website:</p>
    <p>(Alert: SSRF vulnerability)</p>
    <form method="GET" action="ssrf.php">
        <label>Target URL:</label>
        <input type="text" name="target_url" placeholder="e.g. http://example.com" required>
        <input type="submit" value="Fetch Content">
    </form>
    <?php if ($result): ?>
    <div class="result">
        <?php echo $result; ?>
    </div>
    <?php endif; ?>
</main>
</body>
</html>
