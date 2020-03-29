<?php
session_start();
require_once 'config.php';

if (array_key_exists($language, Array('DE' => null, 'EN' => null)))
{
    require_once 'lang/'.$language.'.php';
} else {

    require_once 'lang/EN.php';
}

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

    switch ($siteget) {

        case 'Diagramm':
            $seite = 'screens/diagramm.php';
            $webseittitel = 'Diagramm';
            $passwordrequired = false;
            break;
        case 'DiagrammWoche':
            $seite = 'screens/diagrammWoche.php';
            $webseittitel = 'DiagrammWoche';
            $passwordrequired = false;
            break;
        case 'Einstellung':
            $seite = 'screens/einstellungen.php';
            $webseittitel = 'Einstellung';
            $passwordrequired = true;
            break;
        case 'suchen':
            $seite = 'screens/suchen.php';
            $webseittitel = 'Tankstelle suchen';
            $passwordrequired = true;
            break;
        case 'bearbeiten':
            $seite = 'screens/edit.php';
            $webseittitel = $languagetext['edit']['title'];
            $passwordrequired = true;
            break;
        case 'loeschen':
            $seite = 'screens/loeschen.php';
            $webseittitel = 'löschen';
            $passwordrequired = true;
            break;
        case 'login':
            $seite = 'screens/login.php';
            $webseittitel = 'login';
            $passwordrequired = false;
            break;
        default:
            $seite = 'screens/info.php';
            $webseittitel = 'Info';
            $passwordrequired = false;
            break;
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