<?php
require_once '../include/db.inc.php';
session_start();
$referer = $_SERVER['HTTP_REFERER'];

$login = $_POST['login'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmpassword'];

if (empty($password) || ($password !== $confirmPassword)) {
    header("refresh:5;url=$referer");
    die("password is incorrect");
}

$hash = md5($password);
$query  = "INSERT INTO users(id,login,password) VALUES(nextval('user_id'), $1, $2)";
$result = pg_query_params($dbconn, $query, array($login, $hash));
if (!$result) {
    header("refresh:5;url=$referer");
    die("user account creation failed");
}

$_SESSION['login'] = $login;
$referer = $_SERVER['HTTP_REFERER'];
header('Location: '.$referer);
