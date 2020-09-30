<?php
$lang = 'en.php';
if (array_key_exists('lang',$_GET)) {
    $lang = $_GET['lang'];
}
$lang = $lang;
//print_r(stream_get_wrappers());
print_r(stream_get_filters());

include('php://filter/convert.base64-encode/resource=index');
//include($lang.'.php');
include_once($lang);

?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            .hint {color: white}
        </style>
    </head>
    <body>
            <form action="#" method="GET">
                <input type="radio" name="lang" value="en.php" id="en" <?= ($lang==='en.php')?'checked':'' ?> ><label for="en">English</label>
                <input type="radio" name="lang" value="fr.php" id="fr" <?= ($lang==='fr.php')?'checked':'' ?> ><label for="fr">Français</label>
                <button type="submit">Change</button>
            </form>
            <hr/>
        <?php
            echo $trad['hi'].'<br/>'.$trad['welcome'];
        ?>

        <hr/><a href="/">Back home</a>
        <div class="hint">
        http://localhost:8080/lfi/?lang=../sqli/index#
        http://localhost:8080/lfi/?lang=../../../log/apache2/access.log\0x00#
        </div>
    </body>
</html>