<?php
require_once '../include/db.inc.php';
session_start();

$login = $_POST['login'];
$password = $_POST['password'];

$query  = "SELECT password FROM users WHERE login=$1";
$result = pg_query_params($dbconn, $query, array($login));
if (!$result)
    die('query execution failed');

$storedHash = @pg_fetch_result($result, 0,0);

unset($_SESSION['login']);
if (password_verify($password, $storedHash)) {
    $_SESSION['login'] = $login;
}

header('Location: '.$_SERVER['HTTP_REFERER']);
