<?php require_once(__DIR__ . "/../lib/helpers.php"); ?>
<div class="container">

    <footer class="py-3 my-4">
        <?php if (!is_logged_in()) : ?>
            <ul class="nav justify-content-center border-bottom pb-3 mb-3">
                <li class="nav-item"><a href="index.php" class="nav-link px-2 text-muted">Home</a></li>
                <li class="nav-item"><a href="login.php" class="nav-link px-2 text-muted">Login</a></li>
                <li class="nav-item"><a href="register.php" class="nav-link px-2 text-muted">Sign Up</a></li>
            </ul>
        <?php endif; ?>
        <?php if (is_logged_in()) : ?>
            <h3 class="px-2 text-muted text-center">Trickster Trivia</h3>
        <?php endif; ?>
        <p1 class="text-center text-muted">&copy;2022 Copyright @JiaZhong @JavierArtiga @SmitJoshi @DominicQuitoni @EmilyHontiveros</p1>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</div>