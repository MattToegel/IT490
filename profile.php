<!doctype html>
<html lang="en">

<head>
    <title>Profile</title>
    <meta name="description" content="test">
    <meta name=" viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="static/css/style.css">
</head>
<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<h1>My Profile</h1>
<h3>Name: <?php echo get_user_fullname(); ?></h3>
<h4>Email: <?php echo get_email(); ?></h4>
<h4>Username: <?php echo get_username(); ?></h4>
<h5>Age: <?php echo get_age(); ?></h5>

<?php include_once(__DIR__ . "/partials/footer.php"); ?>

</html>