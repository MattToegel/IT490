<!doctype html>
<html lang="en">
<?php require_once(__DIR__ . "/lib/helpers.php");
if (is_logged_in()) {
    header("Location: home.php");
}
?>

<head>
    <title><?php echo ucfirst(substr(basename(__FILE__), 0, -4)); ?></title>
    <?php require_once(__DIR__ . "/partials/header.php"); ?>
</head>

<body>
    <?php require_once(__DIR__ . "/partials/nav.php"); ?>
    <div class="container">
        <h1>Homepage</h1>
        <p>To be the best, you will have to beat the best!
            Join the competitive environment of quizzes where players get to challenge the world leaderboard. Show off your glory by showcasing your achievement through hard-earned badges. Not in a competitive mood. No problem, users can create trivia and play with their family and friends. Join our wonderful Trivia community for the sake of glory and entertainment by logging in with your email. New to the platform, no problem, sign up today to join the community:</p>
        <button type="button" href="login.php">Login</button>
        <button type="button" href="register.php">Register Here</button>

    </div>
    <?php include_once(__DIR__ . "/partials/footer.php"); ?>
  
</body>

</html>
