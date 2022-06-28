<!doctype html>
<html lang="en">
<?php
require_once(__DIR__ . "/lib/helpers.php");
if (!is_logged_in()) {
    die(header("Location: index.php"));
}
?>

<head>
    <title><?php echo ucfirst(substr(basename(__FILE__), 0, -4)); ?></title>
    <?php require_once(__DIR__ . "/partials/header.php"); ?>
</head>

<body>
    <?php require_once(__DIR__ . "/partials/nav.php"); ?>


    <?php
    require_once(__DIR__ . "/lib/helpers.php");
    echo '<p>Welcome ' . get_user_fullname() . '</p>';
    ?>
    <form action="logout.php">
        <input type="submit" value="Log Out" />
    </form>

    <?php include_once(__DIR__ . "/partials/footer.php"); ?>
    </body>

</html>
