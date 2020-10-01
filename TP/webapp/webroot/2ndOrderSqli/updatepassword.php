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

$query  = "SELECT id,password FROM users WHERE login=$1";
$result = pg_query_params($dbconn, $query, array($login));
if (!$result)
    die('query execution failed');

$row = @pg_fetch_assoc($result, 0);
$storedHash = $row['password'];
if (password_verify($password, $storedHash)) {
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    $id = $row['id'];
    $query  = "UPDATE users SET password=$1 WHERE id=$2";
    $result = pg_query_params($dbconn, $query, array($newPasswordHash, $id));
    if (!$result)
        die('update password failed');
}

header('Location: '.$referer);
