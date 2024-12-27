<?php
session_start();

$search = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Turkish University Advice Website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0; padding: 0;
        }
        header {
            background: #333; color: #fff; padding: 10px 20px;
        }
        header nav {
            display: flex; justify-content: space-between;
        }
        header a {
            color: #fff; text-decoration: none; margin-left: 20px;
        }
        main { padding: 20px; }
        .search-bar { margin-bottom: 20px; }
        input[type="text"] { padding: 6px; width: 200px; }
        input[type="submit"] {
            padding: 6px 10px; background: #333; color: #fff;
            border: none; cursor: pointer;
        }
        ul { list-style-type: none; padding: 0; }
        li {
            background: #fff; margin-bottom: 10px; padding: 10px;
            border: 1px solid #ddd;
        }
        li a {
            text-decoration: none; color: #333;
        }
        li a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<header>
    <nav>
        <div>
            <strong>Turkish University Advice</strong>
        </div>
        <div>
            <?php if (isset($_SESSION['username'])): ?>
                <span style="color:#fff; margin-right:20px;">
                    Hello, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); //Pathced: Used htmlspecialchars to eliminate injections ?>
                </span>
                <a href="index.php">Home</a>
                <a href="update_password.php">Update Password</a>
                <a href="logout.php">Logout</a>
                <a href="upload_vulnerable.php">Upload Your Transcript</a>
                <a href="path_traversal.php">University Rankings</a>
                <a href="ssrf.php">Fetch News</a>
            <?php else: ?>
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
<main>
    <h1>Recent News</h1>
    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search..." 
                   value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="submit" value="Search">
        </form>
    </div>
    <?php
    if (!empty($search)) {
        // Patched: Escaped user input using htmlspecialchars this fixed the injection errors
        echo "<p>Results for: " . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . "</p>";
    }

    // Simple RSS fetch
    $rss_url = "https://www.hurriyet.com.tr/rss/egitim";
    $rss = @simplexml_load_file($rss_url);
    if ($rss === false) {
        echo "<p>Could not fetch RSS feed.</p>";
    } else {
        echo "<ul>";
        foreach ($rss->channel->item as $item) {
            $title = (string)$item->title;
            $description = (string)$item->description;
            $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

            if (!empty($search)) {
                if (stripos($title, $search) !== false || stripos($description, $search) !== false) {
                    echo "<li><a href='" . htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8') 
                         . "' target='_blank'>{$safeTitle}</a></li>";
                }
            } else {
                echo "<li><a href='" . htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8') 
                     . "' target='_blank'>{$safeTitle}</a></li>";
            }
        }
        echo "</ul>";
    }
    ?>
</main>
</body>
</html>
