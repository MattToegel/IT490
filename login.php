<!doctype html>
<html lang="en">

<head>
    <title>Registration</title>
    <meta name="description" content="test">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="static/css/style.css">
</head>

<body>
<nav class="navbar navbar-expand-lg bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="Logo.png" alt="Logo" height="36">&nbsp Trivia
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link "  href="#">Signup</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
<?php
require_once(__DIR__ . "/lib/helpers.php");
?>
    <form id="login" class="form" method="POST">
        <h4>Log In Here</h4>
        <label for="user_email">Username</label>
        <input type="text" id="user_email" name="user_email" />
        <br>
        <label for="pass">Password</label>
        <input type="password" id="pass" name="pass" required />
        <br>
        <input type="submit" id="l_submit" name="submit" value="Log In" />
    </form>
    <h4>Don't have an account? <a href="register.php">Register Here</a></h4>
    <div class="container">

<footer class="py-3 my-4">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
        <li class="nav-item"><a href="index.php" class="nav-link px-2 text-muted">Home</a></li>
        <li class="nav-item"><a href="login.php" class="nav-link px-2 text-muted">Login</a></li>
        <li class="nav-item"><a href="register.php" class="nav-link px-2 text-muted">Sign Up</a></li>
    </ul>
    <p1 class="text-center text-muted">&copy;2022 Copyright @JiaZhong @JavierArtiga @SmitJoshi @DominicQuitoni @EmilyHontiveros</p1>
</footer>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>


<?php
if (isset($_POST["submit"])) {
    $user_email = null;
    $pass = null;

    if (isset($_POST["user_email"])) {
        $user_email = $_POST["user_email"];
    }

    if (isset($_POST["pass"])) {
        $pass = $_POST["pass"];
    }
    $isValid = true;
    if (!isset($user_email) || !isset($pass)) {
        $isValid = false;
    }

    if ($isValid) {
        $db = getDB();
//        echo "is valid";
        if (isset($db)) {
            $query = "SELECT * FROM Users WHERE email = :user_email OR username = :user_email AND `is_active` = 1";
            $stmt = $db->prepare($query);
            $params = array(":user_email" => $user_email);
            $r = $stmt->execute($params);
            $e = $stmt->errorInfo();
            if ($e[0] != "00000") {
                echo "Something went wrong";
            } else {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result && isset($result["password"])) {
                    $password_hash = $result["password"];
                    if (password_verify($pass, $password_hash)) {
                        unset($result["password"]);
                        set_sess_var("fname", $result["fname"]);
                        set_sess_var("lname", $result["lname"]);
                        set_sess_var("username", $result["username"]);
                        set_sess_var("email", $result["email"]);
                        set_sess_var("id", $result["id"]);
                        header("Location:home.php");
                    }
			else {
				echo "Wrong username or password";
			}
                }
            }
        }
    }
}
?>
