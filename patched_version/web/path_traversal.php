<?php
session_start();

//Check if logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$result = "";
$baseDir = __DIR__ . "/files/";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    // Patched: We sanitize the filename to prevent directory traversal
    $filename = basename($_POST['filename']); 
    // Or we could have also used this as well: $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $_POST['filename']);

    // Use realpath to get the absolute path
    $targetPath = realpath($baseDir . $filename);

    
    if ($targetPath && strpos($targetPath, realpath($baseDir)) === 0) {
        if (file_exists($targetPath)) {
            $fileContent = file_get_contents($targetPath);
            // Again we used htmlspecialchars to avoid XSS vulnerability
            $result = htmlspecialchars($fileContent, ENT_QUOTES, 'UTF-8');
        } else {
            $result = "File not found.";
        }
    } else {
        $result = "Invalid file path.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Path Traversal Demo</title>
    <style>
        body { font-family: Arial,sans-serif; background:#f5f5f5; margin:0; padding:0; }
        header { background:#333; color:#fff; padding:10px 20px; }
        header nav a { color:#fff; text-decoration:none; margin-right:20px; }
        main {
            padding:20px; max-width:600px; margin:40px auto; background:#fff;
            border:1px solid #ddd;
        }
        form label { display:block; margin-bottom:5px; }
        input[type="text"] {
            padding:6px; width:100%; margin-bottom:15px; border:1px solid #ccc;
        }
        input[type="submit"] {
            padding:10px; background:#333; color:#fff; border:none; cursor:pointer;
        }
        .result {
            margin-top:20px; background:#eee; padding:10px; word-wrap:break-word;
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
    <h1>Check For University Rankings</h1>
    <p>Below we created txt files about university rankings, you can find detailed information about university department rankings:</p>
    <p>(Caution: Path traversal vulnerability)</p>
    <form method="POST" action="path_traversal.php">
        <label>Filename:</label>
        <input type="text" name="filename" placeholder="e.g. Sabanci_University.txt, Koc_University.txt etc." required>
        <input type="submit" value="View File">
    </form>
    <?php if ($result): ?>
    <div class="result">
        <?php echo nl2br($result); ?>
    </div>
    <?php endif; ?>
</main>
</body>
</html>
