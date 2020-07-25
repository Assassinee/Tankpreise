<?php
session_start();
require_once 'config/config.php';
require_once 'config/sites.php';
require_once 'lang/loadLang.php';

$seite = '';
$webseittitel = '';
$passwordrequired = false;

if(isset($_GET['benzinart']))
{
    setcookie('benzinart', $_GET['benzinart'], time() + 60 * 60 * 24 * 7 * 4);

    $url = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], 'benzinart=') - 1);

    header("Location: $url");
}

if (isset($_COOKIE['benzinart']))
{
    $BENZINART = $_COOKIE['benzinart'];
} else {

    $BENZINART = $diagramm['benzinart'];
}

if($webseitenzugriff == 0 && $_SESSION['eingeloggt'] != true) {

    $seite = 'screens/login.php';
    $webseittitel = 'Login';

} else {

    $siteget = '';

    if(isset($_GET['site']))
    {
        $siteget = $_GET['site'];
    }

    if (key_exists($siteget, $sites))
    {
        $seite = $sites[$siteget]['site'];
        $webseittitel = $sites[$siteget]['title'];
        $passwordrequired = $sites[$siteget]['password'];
    }
    else
    {
        $seite = $sites['default']['site'];
        $webseittitel = $sites['default']['title'];
        $passwordrequired = $sites['default']['password'];
    }

    //TODO: Notice: Undefined index: eingeloggt in /Users/max/website/tankpreise/index.php on line 85
    if ($passwordrequired && @$_SESSION['eingeloggt'] != true)
    {
        $redirectsite = $seite;
        $redirecttitle = $webseittitel;

        $seite = 'screens/login.php';
        $webseittitel = 'Login';
    }
}

require_once('header.php');
require_once($seite);
require_once('footer.php');
?>