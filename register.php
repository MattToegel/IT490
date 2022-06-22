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
    <?php require_once(__DIR__ . "/partials/nav.php"); ?>

    <div class="container">

        <div id="r-form">
            <form method="post" id="reg-test">
                <label for="fname">First Name</label>
                <input type="text" id="fname" name="fname" value="<?php if (!isset($_POST["fname"])) {
                                                                        echo '';
                                                                    } else {
                                                                        echo $_POST["fname"];
                                                                    } ?>" required />
                <br>
                <label for="lname">Last Name</label>
                <input type="text" id="lname" name="lname" value="<?php if (!isset($_POST["lname"])) {
                                                                        echo '';
                                                                    } else {
                                                                        echo $_POST["lname"];
                                                                    } ?>" required />
                <br>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php if (!isset($_POST["username"])) {
                                                                            echo '';
                                                                        } else {
                                                                            echo $_POST["username"];
                                                                        } ?>" required />
                <br>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php if (!isset($_POST["email"])) {
                                                                        echo '';
                                                                    } else {
                                                                        echo $_POST["email"];
                                                                    } ?>" required />
                <br>
                <label for="bday">Birthday</label>
                <input type="date" id="bday" name="bday" value="<?php if (!isset($_POST["bday"])) {
                                                                    echo '';
                                                                } else {
                                                                    echo $_POST["bday"];
                                                                } ?>" required />
                <br>
                <label for="pass">Password</label>
                <input type="password" id="pass" name="pass" required />
                <br>
                <label for="pass1">Confirm Password</label>
                <input type="password" id="pass1" name="pass1" required />
                <br>
                <input type="submit" id="r_submit" name="submit" value="Register" />
            </form>
        </div>
    </div>

    <?php include_once(__DIR__ . "/partials/footer.php"); ?>



    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>


    <?php

    require_once(__DIR__ . "/lib/helpers.php");
    if (isset($_POST["submit"])) {
        $fname = null;
        $lname = null;
        $email = null;
        $username = null;
        $bday = null;
        $is_active = 1;
        $password = null;
        $password1 = null;

        if (isset($_POST["fname"])) {
            $fname = $_POST["fname"];
        }

        if (isset($_POST["lname"])) {
            $lname = $_POST["lname"];
        }

        if (isset($_POST["email"])) {
            $email = $_POST["email"];
        }

        if (isset($_POST["username"])) {
            $username = $_POST["username"];
        }

        if (isset($_POST["bday"])) {
            $bday = $_POST["bday"];
        }

        if (isset($_POST["pass"])) {
            $password = $_POST["pass"];
        }

        if (isset($_POST["pass1"])) {
            $password1 = $_POST["pass1"];
        }

        $isValid = true;

        if (strlen($username) < 8 || strlen(($username) > 32)) {
            echo "Username must be between 8 and 32 characters";
            $isValid = false;
        }

        if (strlen($password) < 8) {
            echo "Password must be 8 characters or more";
            $isValid = false;
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d,.;:]).+$/', $password)) {
            echo "Password must contain a capital letter, a number, and a special character";
            $isValid = false;
        }

        if ($password != $password1) {
            echo "Passwords don't match";
            $isValid = false;
        }

        if (!isset($email) || !isset($username) || !isset($fname) || !isset($bday) || !isset($password) || !isset($password)) {
            echo "Unexpected error";
            $isValid = false;
        }

        if ($isValid) {
            $pass_hash = password_hash($password, PASSWORD_BCRYPT);

            $registration_array = array(
                "fname" => $fname,
                "lname" => $lname,
                "email" => $email,
                "username" => $username,
                "bday" => $bday,
                "is_active" => $is_active,
                "pass" => $pass_hash,
                "type" => $_POST["submit"]
            );
            require_once(__DIR__ . "/rpc_producer.php");
            $reg_rpc = new RpcClient();
            $response = $reg_rpc->call($registration_array, 'reg_queue');
            // echo var_dump($response);
            if ($response == "0") {
                echo "Registration Successful";
            } elseif ($response == "1") {
                echo "Account already exists. Please log in.";
            } else {
                echo "An error occurred during registration. Please try again";
            }
        }
    }


    ?>


</body>

</html>