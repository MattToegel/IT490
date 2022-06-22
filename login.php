<!doctype html>
<html lang="en">

<head>
    <title>Log In</title>
    <meta name="description" content="test">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="static/css/style.css">
</head>

<body>
    <?php require_once(__DIR__ . "/partials/nav.php"); ?>

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
        <?php include_once(__DIR__ . "/partials/footer.php"); ?>

        <?php
        if (isset($_POST["submit"])) {
            $user_email = null;
            $pass = null;
            $_POST["type"] = $_POST["submit"];

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

                require_once(__DIR__ . "/rpc_producer.php");
                $login_rpc = new RpcClient();
                $response = json_decode($login_rpc->call($_POST, 'login_queue'), true);
                echo var_dump($response);
                if ($response == "1") {
                    echo "Login unsuccessful";
                } else {
                    set_sess_var("fname", $response["fname"]);
                    set_sess_var("lname", $response["lname"]);
                    set_sess_var("username", $response["username"]);
                    set_sess_var("email", $response["email"]);
                    set_sess_var("id", $response["id"]);
                    set_sess_var("bday", $response["bday"]);
                    header("Location:home.php");
                }
            }
        }
        ?>