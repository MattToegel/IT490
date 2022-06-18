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
        echo "is valid";
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
                }
            }
        }
    }
}
?>
