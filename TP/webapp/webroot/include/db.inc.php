<?php

$password = @file_get_contents(getenv('PGPASSFILE'));
$cs = "password=$password";

$dbconn = @pg_connect($cs);
if (!$dbconn) {
    die('could not connect to pg database');
}


function execQuery($query, $verbose = false) {
    if ($verbose) {
        echo '<pre>' . $query . '</pre>';
    }
    if (!pg_query($query)) {
        die($query);
    }
}
