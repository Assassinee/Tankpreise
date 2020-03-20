<?php
session_start();
require_once 'config.php';

$seite = '';
$webseittitel = '';

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

    $seite = 'login.php';
    $webseittitel = 'Login';
} else {

    $siteget = '';

    if(isset($_GET['site']))
    {
        $siteget = $_GET['site'];
    }

    switch ($siteget) {

        case 'Diagramm':
            $seite = 'diagramm.php';
            $webseittitel = 'Diagramm';
            break;
        case 'DiagrammWoche':
            $seite = 'diagrammWoche.php';
            $webseittitel = 'DiagrammWoche';
            break;
        case 'Einstellung':
            $seite = 'einstellungen.php';
            $webseittitel = 'Einstellung';
            break;
        case 'suchen':
            $seite = 'suchen.php';
            $webseittitel = 'Tankstelle suchen';
            break;
        case 'bearbeiten':
            $seite = 'bearbeiten.php';
            $webseittitel = 'bearbeiten';
            break;
        case 'loeschen':
            $seite = 'loeschen.php';
            $webseittitel = 'löschen';
            break;
        case 'info':
            $seite = 'info.php';
            $webseittitel = 'Info';
            break;
        default:
            $seite = 'info.php';
            $webseittitel = 'Info';
            break;
    }
}

require_once('header.php');
require_once($seite);
require_once('footer.php');
?>