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

            //$login = pg_escape_string($login);
            $hash = md5($password);

            /* VULNERABLE CODE: query is injectable */
            $query  = "SELECT COUNT(*) FROM users WHERE login='$login' AND password='$hash';";
            $result = pg_query($query);
            if (!$result)
                die('select users failed');

            while ($row = pg_fetch_row($result)) {
                echo "$row[0] user matching your criteria<br />";
            }
        }
        ?>
        <hr/><a href="/">Back home</a>
    </body>
</html>