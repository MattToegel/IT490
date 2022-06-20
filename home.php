<!doctype html>
<html lang="en">
<head>
    <title>Test</title>
    <meta name="description" content="test">
    <meta name=" viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="static/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand">
            <img src="Logo.png" alt="Logo" height="36">&nbsp Trivia
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">User Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Create Room</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Join Room</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Log Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


    <?php
require_once(__DIR__ . "/lib/helpers.php");
echo '<p>Welcome ' . get_user_fullname() . '</p>';
?>
<form action="logout.php">
    <input type="submit" value="Log Out" />
</form>

<footer class="py-3 my-4">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
        <li class="nav-item"><a href="home.php" class="nav-link px-2 text-muted">Home</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">User Profile</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Dashboard</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Create Game Room</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Join Game Room</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Leaderboard</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link px-2 text-muted">Log Out</a></li>
    </ul>
    <p1 class="text-center text-muted">&copy;2022 Copyright @JiaZhong @JavierArtiga @SmitJoshi @DominicQuitoni @EmilyHontiveros</p1>
</footer>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body></html>
