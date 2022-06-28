<?php
session_start(); //Starting a new sesion
require_once(__DIR__ . "/db.php");

function set_sess_var($sess_name, $db_var)
{
    $_SESSION[$sess_name] = $db_var;
}

function get_user_id()
{
    if (isset($_SESSION["id"])) {
        return $_SESSION["id"];
    }
}

function get_user_fullname()
{
    if (isset($_SESSION["fname"]) && isset($_SESSION["lname"])) {
        return $_SESSION["fname"] . " " . $_SESSION["lname"];
    }
}

function get_username()
{
    if (isset($_SESSION["username"])) {
        return $_SESSION["username"];
    }
}

function get_email()
{
    if (isset($_SESSION["email"])) {
        return $_SESSION["email"];
    }
}

function get_birthday()
{
    if (isset($_SESSION["bday"])) {
        return $_SESSION["bday"];
    }
}

function get_age()
{
    $bday = get_birthday();
    $today = date("Y-m-d");
    $diff = date_diff(date_create($bday), date_create($today));
    return $diff->format('%y');
}

function is_logged_in()
{
    if (isset($_SESSION["id"]) && isset($_SESSION["username"]) && isset($_SESSION["email"])) {
        return true;
    } else {
        return false;
    }
}

//write logs to file
function write_log($log_msg, $log_file)
{
    date_default_timezone_set('America/New_York');
    $timestamp = date("Y-m-d H:i:s");
    $log_dir = __DIR__ . "/../logs/";
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    $log_path = $log_dir . $log_file;
    $log = fopen($log_path, "a");
    fwrite($log, "[ " . $timestamp . " ] " . $log_msg . "\n");
    fclose($log);
}
