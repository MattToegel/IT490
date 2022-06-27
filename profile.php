<!doctype html>
<html lang="en">

<head>
    <title><?php echo ucfirst(substr(basename(__FILE__), 0, -4)); ?></title>
    <?php require_once(__DIR__ . "/partials/header.php"); ?>
</head>
<script src="/static/js/updateProfile.js"></script>
<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<body>
    <div class="card">
        <img src="https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/dog-puppy-on-garden-royalty-free-image-1586966191.jpg?crop=1.00xw:0.669xh;0,0.190xh&resize=1200:*" alt="User Profile" style="width:200px; height: 200px; border-radius:50%">
        <div class="flex-grow-1 ms-3">
            <div id="profile-form">
                <h4 class="mb-1">Name:<?php echo get_user_fullname(); ?></h4>
                <h4 class="mb-1">Email:<?php echo get_email(); ?></h4>
                <h4 class="mb-1">Username:<?php echo get_username(); ?></h4>
                <h4 class="mb-1">Age:<?php echo get_age(); ?></h4>
                <input type="button" id="edit" name="edit" value="Update Profile" />
            </div>
            <form id="profile-confirm-form" method="post" style="display: none;">
                <input type="number" id="userid" name="userid" value="<?php echo get_user_id(); ?>" hidden readonly />
                <label for="new_username">Username: </label>
                <input type="text" id="new_username" name="new_username" placeholder="New Username" />
                <label for="current-password">Current Password</label>
                <input type="password" id="current-password" name="current-password" placeholder="Current Password" />
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new-password" placeholder="New Password" />
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" />
                <input type="button" id="cancel" name="cancel" value="Cancel Update" />
                <input type="submit" id="submit" name="submit" value="Confirm Update" />
            </form>
        </div>
    </div>
</body>
<?php
if (isset($_POST["submit"])) {
    $uid = null;
    $username = null;
    $current_password = null;
    $new_password = null;
    $confirm_password = null;
    if (isset($_POST["userid"])) {
        $uid = $_POST["userid"];
    }
    if (isset($_POST["new_username"])) {
        $username = $_POST["new_username"];
    }
    if (isset($_POST["current-password"])) {
        $current_password = $_POST["current-password"];
    }
    if (isset($_POST["new-password"])) {
        $new_password = $_POST["new-password"];
    }
    if (isset($_POST["confirm-password"])) {
        $confirm_password = $_POST["confirm-password"];
    }

    $isValid = true;
    if ($current_password == null || $uid == null) {
        echo "Please enter your current password";
        $isValid = false;
    }

    if ($username == get_username()) {
        $isValid = false;
    }

    if ($username == null) {
        $username = get_username();
    }


    if ($new_password == null) {
        $new_password = "";
    }

    if ($new_password != $confirm_password) {
        $isValid = false;
    }

    if ($isValid) {
        $update_array = array(
            "uid" => $uid,
            "username" => $username,
            "current_password" => $current_password,
            "new_password" => $new_password
        );

        require_once(__DIR__ . "/rpc_producer.php");
        $update_rpc = new RpcClient();
        $response = json_decode($update_rpc->call($update_array, 'update_queue'), true);
        echo var_dump($response);
        if ($response["status"] == "success") {
            echo "Profile Updated";
            unset($_SESSION["username"]);
            set_sess_var("username", $response["username"]);
            header("Location: /profile.php");
        } else {
            echo "Error Updating Profile";
        }
    }
}

?>


<?php include_once(__DIR__ . "/partials/footer.php"); ?>

</html>