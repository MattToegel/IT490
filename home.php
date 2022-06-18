<?php
require_once(__DIR__ . "/lib/helpers.php");
if (!is_logged_in()) {
    die(header("Location:index.php"));
}

echo '<p>Welcome ' . get_user_fullname() . '</p>';

?>
<a href="logout.php">Logout</a>

