
<?php
require_once(__DIR__ . "/lib/configrmq.php");
require_once(__DIR__ . "/vendor/autoload.php");
//include(__DIR__ . "/config_rmq.php");
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

if (isset($_POST["submit"])) {
    $fname = null;
    $lname = null;
    $username = null;
    $email = null;
    $bday = null;
    $pass = null;
    $pass1 = null;
    $type = $_POST["submit"];

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

    if (isset($_POST["pass1"])) {
        $pass1 = $_POST["pass1"];
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
            ":type" => $type
        );

        echo var_dump($_POST);
        echo "\n\n----\n";
        echo var_dump($reg_arr);
        $test = json_encode($reg_arr);
        echo var_dump($test);

        /* $db = getDB();
        $query = "INSERT INTO Users(fname, lname, email, username, bday, is_active, `password`) ";
        $query .= "VALUES(:fname, :lname, :email, :username, :bday, :is_active, :pass)";
        $stmt = $db->prepare($query);
        $r = $stmt->execute($reg_arr);
        $e = $stmt->errorInfo();
        if ($e[0] == "00000") {
            echo "Registration successful";
        }
        else {
            echo "something went wrong";
        } */
        $connection = new AMQPStreamConnection($brokerhost, $brokerport, $brokeruser, $brokerpass);
        $channel = $connection->channel();
        $channel->queue_declare('reg_queue', false, false, false, false);
    

        $messageBody = json_encode($_POST);
        $message = new AMQPMessage($messageBody);
        $channel->basic_publish($message, '', 'reg_queue');

        $channel->close();
        $connection->close();

		header('Location: login.php');
           
        }

        else {
            header('Location: register.php');
        }
    }

?>

