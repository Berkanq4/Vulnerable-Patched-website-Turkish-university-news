<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Basic check for logged-in user
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$result = "";
$baseDir = __DIR__ . "/files/"; // place files here for demo

if (isset($_POST['filename'])) {
    $filename = $_POST['filename'];
    // Vulnerable path construction
    $targetPath = $baseDir . $filename;

    if (file_exists($targetPath)) {
        // Output file content
        $result = file_get_contents($targetPath);
    } else {
        $result = "File not found.";
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
        <?php echo nl2br(htmlspecialchars($result)); ?>
    </div>
    <?php endif; ?>
</main>
</body>
</html>
