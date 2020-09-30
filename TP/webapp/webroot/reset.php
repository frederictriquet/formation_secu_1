<?php
require_once 'include/db.inc.php';
?>
<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <?php
            $users = [
                ['admin', 'password'],
                ['alice', 'password'],
                ['bob', 'abc123'],
                ['carol', 'P4ssw0rd!'],
                ['dave', 'something']
            ];
            $sql = [
                'DROP TABLE IF EXISTS users;',
                'DROP SEQUENCE IF EXISTS user_id;',
                'CREATE SEQUENCE user_id INCREMENT 1 START 1;',
                'CREATE TABLE public.users (
                    id BIGINT NOT NULL,
                    login CHAR(32) NOT NULL,
                    password CHAR(32) NOT NULL
                );'
            ];

            foreach ($sql as $query) {
                execQuery($query, true);
            }

            foreach ($users as $user) {
                $login = $user[0];
                // VULNERABLE CODE: do not use MD5 sum to hash and store passwords
                $hash = md5($user[1]);
                $query = "INSERT INTO users(id,login,password) VALUES (nextval('user_id'), '$login', '$hash')";
                execQuery($query, true);
            }
        ?>
        <br/>
        Database successfully initialised/reset.
        <hr/><a href="/">Back home</a>.
    </body>
</html>
