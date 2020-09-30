<?php
require_once '../include/db.inc.php';
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            .hint {color: white}
        </style>
    </head>
    <body>
        <?php
            if (!array_key_exists('login',$_SESSION)):
        ?>
            Please login or or register a new account<hr/>
            LOGIN<br/>
            <form action="login.php" method="POST">
                Login <input type="text" name="login"><br/>
                Password <input type="password" name="password"><br/>
                <input type="submit">
            </form>
            <hr/>
            REGISTER<br/>
            <form action="register.php" method="POST">
                Login <input type="text" name="login"><br/>
                Password <input type="password" name="password"><br/>
                Confirm Password <input type="password" name="confirmpassword"><br/>
                <input type="submit">
            </form>
        <?php
            else:
        ?>
            Welcome <?= $_SESSION['login'] ?><br/>
            <ul>
                <li><a href="account.php">Account settings</a></li>
                <li><a href="logout.php">Log out</a></li>
            </ul>
        <?php
            endif;
        ?>

        <hr/><a href="/">Back home</a>
        <div class="hint">
            The SQL requests used for user creation and login are safe regarding SQL injections.
            However, one can register a user named "foo' or 1=1 -- ". When this user updates his
            password all the users get the new password.
        </div>
    </body>
</html>