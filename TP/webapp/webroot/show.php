<?php
require_once 'include/db.inc.php';
?>
<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <table border='1px'>
            <tr><th>id</th><th>login</th><th>hash</th></tr>
        <?php
            $query = 'select id, login, password from users';
            $result = pg_query($query);
            while ($row = pg_fetch_row($result)) {
                echo '<tr><td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td></tr>';
            }
        ?>
        </table>
        <a href="/">Back home</a>.
    </body>
</html>
