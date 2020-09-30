<?php
require_once '../include/db.inc.php';
session_start();
?>
<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <?php
            if (!array_key_exists('login',$_SESSION)):
                header('Location: '.$_SERVER['HTTP_REFERER']);
                die();
            else:
        ?>
            Welcome <?= $_SESSION['login'] ?><br/>
            CHANGE PASSWORD<br/>
            <form action="updatepassword.php" method="POST">
                Current Password <input type="password" name="password"><br/>
                New Password <input type="password" name="newpassword"><br/>
                Confirm new Password <input type="password" name="confirmnewpassword"><br/>
                <input type="submit">
            </form>
            <ul>
                <li><a href="account.php">Account settings</a></li>
                <li><a href="logout.php">Log out</a></li>
            </ul>
        <?php
            endif;
        ?>

        <hr/><a href="/">Back home</a>
    </body>
</html>