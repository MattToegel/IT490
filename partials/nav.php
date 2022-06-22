<?php require_once(__DIR__ . "/../lib/helpers.php"); ?>

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
                <?php if (is_logged_in()) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">User Profile</a>
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
                <?php endif; ?>

                <?php if (!is_logged_in()) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Log In</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>