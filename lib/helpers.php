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

function get_party_num()
{
    if (isset($_SESSION["party_num"])) {
        return $_SESSION["party_num"];
    }
}

function get_party_id()
{
    if (isset($_SESSION["party_id"])) {
        return $_SESSION["party_id"];
    }
}

function is_logged_in()
{
    if (isset($_SESSION["id"]) && isset($_SESSION["username"]) && isset($_SESSION["email"])) {
        return true;
    } else {
        return false;
    }
}