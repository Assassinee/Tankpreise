<?php
if (file_exists('installieren.php'))
{
    header('location: installieren.php');
}
session_start();
require_once 'config/config.php';
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

    switch ($siteget) {
        case 'arround':
            $seite = 'screens/arround.php';
            $webseittitel = $languagetext['arround']['title'];
            $passwordrequired = false;
            break;
        case 'Diagramm':
            $seite = 'screens/diagram.php';
            $webseittitel = $languagetext['diagram']['title'];
            $passwordrequired = false;
            break;
        case 'DiagrammWoche':
            $seite = 'screens/diagrammWoche.php';
            $webseittitel = $languagetext['diagramwoche']['title'];
            $passwordrequired = false;
            break;
        case 'Einstellung':
            $seite = 'screens/settings.php';
            $webseittitel = $languagetext['settings']['title'];
            $passwordrequired = true;
            break;
        case 'suchen':
            $seite = 'screens/search.php';
            $webseittitel = $languagetext['search']['title'];
            $passwordrequired = true;
            break;
        case 'bearbeiten':
            $seite = 'screens/edit.php';
            $webseittitel = $languagetext['edit']['title'];
            $passwordrequired = true;
            break;
        case 'loeschen':
            $seite = 'screens/delete.php';
            $webseittitel = $languagetext['delete']['title'];
            $passwordrequired = true;
            break;
        case 'login':
            $seite = 'screens/login.php';
            $webseittitel = $languagetext['login']['title'];
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