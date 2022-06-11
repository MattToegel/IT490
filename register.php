<html>

<head>
    <title>Test</title>
    <meta name="description" content="test">
    <meta name=" viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div id="heading">
        <h1>Testing registration with rabbitmq</h1>
    </div>
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
            <label for="pass2">Confirm Password</label>
            <input type="password" id="pass2" name="pass2" required />
            <br>
            <input type="submit" id="r_submit" name="submit" value="Register" />
        </form>
    </div>

    <?php
    require_once('path.inc');
    require_once('get_host_info.inc');
    require_once('rabbitMQLib.inc');

    if (isset($_POST["submit"])) {
        $fname = null;
        $lname = null;
        $username = null;
        $email = null;
        $bday = null;
        $pass = null;
        $pass1 = null;

        if (isset($_POST["fname"])) {
            $fname = $_POST["fname"];
        }

        if (isset($_POST["lname"])) {
            $lname = $_POST["lname"];
        }

        if (isset($_POST["username"])) {
            $username = $_POST["username"];
        }

        if (isset($_POST["email"])) {
            $email = $_POST["email"];
        }

        if (isset($_POST["bday"])) {
            $bday = $_POST["bday"];
        }

        if (isset($_POST["pass"])) {
            $pass = $_POST["pass"];
        }

        if (isset($_POST["pass2"])) {
            $pass1 = $_POST["pass2"];
        }

        $isValid = true;

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d,.;:]).+$/', $pass) || strlen($pass) < 8) {
            echo "Password must contain a capital letter, a lowercase letter, a number, and a special character and must be atleast 8 characters";
            $isValid = false;
        }

        if ($pass != $pass1) {
            echo "Passwords don't match";
            $isValid = false;
        }

        if (strlen($username) < 8) {
            echo "Username must be 8 characters or longer";
            $isValid = false;
        }

        if (!isset($email) || !isset($username) || !isset($fname) || !isset($bday) || !isset($pass) || !isset($pass1)) {
            $isValid = false;
        }

        if ($isValid) {
            $pass_hash = password_hash($pass, PASSWORD_BCRYPT);

            $reg_arr = array(
                "fname" => $fname,
                "lname" => $lname,
                "username" => $username,
                "email" => $email,
                "bday" => $bday,
                "pass" => $pass_hash,
                "submit" => $_POST["submit"]
            );

            $json_message = json_encode($reg_arr);
            echo $json_message;
            $msg = array("data" => $json_message, "type" => "insert");
            $client = new RabbitMQClient('testRabbitMQ.ini', 'testServer');
            $response = $client->send_request($msg);
            echo "Login successful";
            print_r($reponse);
            echo "\n\n";
        }
    }
    ?>


</body>

</html>