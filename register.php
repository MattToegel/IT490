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
    //require_once('path.inc');
    //require_once('get_host_info.inc');
    //require_once('rabbitMQLib.inc');
    include(__DIR__ . "/vendor/autoload.php");
    include(__DIR__ . "/config_rmq.php");
    use PhpAmqpLib\Connection\AMQPStreamConnection;
    use PhpAmqpLib\Exchange\AMQPExchangeType;
    use Php\Message\AMQPMessage;
    require_once(__DIR__ . "/lib/helpers.php");

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
                ":fname" => $fname,
                ":lname" => $lname,
                ":email" => $email,
                ":username" => $username,
                ":bday" => $bday,
                ":is_active" => 1,
                ":pass" => $pass_hash,
            );

            $connection = new AMQPStreamConnection($BROKER_HOST, $BROKER_PORT, $USER, $PASSWORD, $VHOST);
            $channel = $connection->channel();
            $channel->queue_declare($QUEUE, false, true, false, false);
            $channel->exchange_declare($EXCHANGE, AMQPExchangeType::DIRECT, false, true, false);
            $channel->queue_bind($QUEUE, $EXCHANGE);

            $messageBody = json_encode($reg_arr, JSON_PRETTY_PRINT);
            $message = new AMQPMessage($messageBody, array('content_type' => 'application/json', 'delievery_mode' => AMQPMessage::DELIEVERY_MODE_PERSISTENT));
            $channel->basic_publish($message, $EXCHANGE);

            $channel->close();
            $connection->close();
            // $db = getDB();
            // $query = "INSERT INTO Users(fname, lname, email, username, bday, is_active, `password`) ";
            // $query .= "VALUES(:fname, :lname, :email, :username, :bday, :is_active, :pass)";
            // $stmt = $db->prepare($query);
            // $r = $stmt->execute($reg_arr);
            // $e = $stmt->errorInfo();
            // if ($e[0] == "00000") {
            //     echo "Registration successful";
            // }
            // else {
            //     echo "something went wrong";
            // }

           
        }
    }
    ?>


</body>

</html>
