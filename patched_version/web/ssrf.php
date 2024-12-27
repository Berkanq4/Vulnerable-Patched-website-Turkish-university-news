<?php
session_start();


// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$result = "";

// Patched: Use a function to validate external URLs, it only accepts http and https protocols and rejects protocols such as file or ftp that is generally used in ssrf.
function isUrlAllowed($url) {
    $parsed = parse_url($url);
    if (!isset($parsed['scheme']) || !in_array($parsed['scheme'], ['http','https'])) {
        return false;
    }
// Patched: Also checks if a valid host is there in the URL and reject if there is non.
    if (!isset($parsed['host'])) {
        return false;
    }
    // Takes a look at the ip and makes sure that ip is not in private/internal ip range (127.0.0.1, 192.168.xx.xx, 10.xx.xx.xx, 169.254.xx.xx)
    $ip = gethostbyname($parsed['host']);
    // Disallow private/local IP range
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return false;
    }
    return true;
}

if (isset($_GET['target_url'])) {
    $url = $_GET['target_url'];

    // Check the URL with isUrlAllowed
    if (!isUrlAllowed($url)) {
        $result = "URL not allowed! Please provide a valid external URL.";
    } else {
        $contents = @file_get_contents($url);
        if ($contents === false) {
            $result = "Could not fetch content.";
        } else {
            //Again protection agains html
            $result = htmlspecialchars($contents, ENT_QUOTES, 'UTF-8');
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
