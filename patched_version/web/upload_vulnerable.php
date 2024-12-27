<?php
session_start();


if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$uploadDir = __DIR__ . "/uploads/";
$message = "";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Patched: Here defined accepted file extensions
$allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg', 'txt'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['profile_file']) && $_FILES['profile_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_file']['tmp_name'];
        $fileName    = $_FILES['profile_file']['name'];
        // We extract the extension as an variable
        $extension   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // And check if it allowed or not
        if (!in_array($extension, $allowedExtensions)) {
            $message = "Invalid file extension. Allowed: " . implode(', ', $allowedExtensions);
        } else {
            // Generate random filename to avoid direct RCE with .php, etc.
            $newFileName = bin2hex(random_bytes(8)) . '.' . $extension;
            $destPath    = $uploadDir . $newFileName;



            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $safeName = htmlspecialchars($newFileName, ENT_QUOTES, 'UTF-8');
                $message = "File uploaded successfully: <a href='uploads/" 
                         . urlencode($newFileName) . "'>" . $safeName . "</a>";
            } else {
                $message = "Error moving uploaded file.";
            }
        }
    } else {
        $message = "No file uploaded or upload error.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unrestricted File Upload Demo</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:0; }
        header { background:#333; color:#fff; padding:10px 20px; }
        header nav a { color:#fff; text-decoration:none; margin-right:20px; }
        main {
            padding:20px; max-width:600px; margin:40px auto; background:#fff;
            border:1px solid #ddd;
        }
        form label { display:block; margin-bottom:5px; }
        input[type="file"] { margin-bottom:15px; }
        input[type="submit"] {
            padding:10px; background:#333; color:#fff; border:none; cursor:pointer;
        }
        .message { margin-top:20px; background:#eee; padding:10px; }
        a { color:#333; text-decoration:none; }
        a:hover { text-decoration:underline; }
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
    <h1>Upload Your Transcript</h1>
    <p>Upload transcript to see which universities you might get accepted:</p>
    <p>(Alert: Unrestricted file upload vulnerability)</p>
    <form method="POST" action="upload_vulnerable.php" enctype="multipart/form-data">
        <label>Choose file:</label>
        <input type="file" name="profile_file" required>
        <input type="submit" value="Upload">
    </form>
    <?php if ($message): ?>
    <div class="message">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
</main>
</body>
</html>
