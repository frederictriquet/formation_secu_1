<?php
require_once '../include/db.inc.php';
session_start();
$referer = $_SERVER['HTTP_REFERER'];

if (!array_key_exists('login',$_SESSION)) {
    header('Location: '.$referer);
    die();
}

$login = $_SESSION['login'];
$password = $_POST['password'];
$newPassword = $_POST['newpassword'];
$confirmNewPassword = $_POST['confirmnewpassword'];

if (empty($newPassword) || ($newPassword !== $confirmNewPassword)) {
    header("refresh:5;url=$referer");
    die("new password is incorrect");
}

$query  = "SELECT password FROM users WHERE login=$1";
$result = pg_query_params($dbconn, $query, array($login));
if (!$result)
    die('query execution failed');

$storedHash = @pg_fetch_result($result, 0,0);
$hash = md5($password);
if ($hash === $storedHash) {
    $newPasswordHash = md5($newPassword);
    /* VULNERABLE CODE */
    $query  = "UPDATE users SET password='$newPasswordHash' WHERE login='$login' and password='$hash'";
    $result = pg_query($query);
    if (!$result)
        die('update password failed');
}

header('Location: '.$referer);
