<?php
session_start();
require_once('config.php');

$seite = '';
$webseittitel = '';

if($webseitenzugriff == 0 && $_SESSION['eingeloggt'] != true) {

    $seite = 'login.php';
    $webseittitel = 'Login';
} else {

    $siteget = '';

    if(isset($_GET['site'])) {
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