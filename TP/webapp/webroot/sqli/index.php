<?php
require_once '../include/db.inc.php';
?>
<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <form action="#" method="POST">
                Login <input type="text" name="login"><br/>
                Password <input type="text" name="password"><br/>
                <input type="submit">
        </form>
        <?php

        if (isset($_POST['login'])) {
            $login = $_POST['login'];
            $password = $_POST['password'];
            echo "login = $login   password = $password <br/>";

            $query  = 'SELECT password FROM users WHERE login=$1';
            $result = pg_query_params($dbconn, $query, array($login));
            if (!$result)
                die('select users failed');
            
            $row = pg_fetch_row($result);
            $storedHash = $row[0];
            $nbPassOK = 0;
            if (password_verify($password, $storedHash))
                $nbPassOK = 1;
            echo "$nbPassOK user matching your criteria<br/>";
        }
        ?>
        <hr/><a href="/">Back home</a>
    </body>
</html>