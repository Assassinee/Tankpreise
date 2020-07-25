<?php
require_once 'lang/loadLang.php';

$sites['arround']['site'] = 'screens/arround.php';
$sites['arround']['title'] = $languagetext['arround']['title'];
$sites['arround']['password'] = false;

$sites['Diagramm']['site'] = 'screens/diagram.php';
$sites['Diagramm']['title'] = $languagetext['diagram']['title'];
$sites['Diagramm']['password'] = false;

$sites['DiagrammWoche']['site'] = 'screens/diagrammWoche.php';
$sites['DiagrammWoche']['title'] = $languagetext['diagramwoche']['title'];
$sites['DiagrammWoche']['password'] = false;

$sites['Einstellung']['site'] = 'screens/settings.php';
$sites['Einstellung']['title'] = $languagetext['settings']['title'];
$sites['Einstellung']['password'] = true;

$sites['suchen']['site'] = 'screens/search.php';
$sites['suchen']['title'] = $languagetext['search']['title'];
$sites['suchen']['password'] = true;

$sites['bearbeiten']['site'] = 'screens/edit.php';
$sites['bearbeiten']['title'] = $languagetext['edit']['title'];
$sites['bearbeiten']['password'] = true;

$sites['loeschen']['site'] = 'screens/delete.php';
$sites['loeschen']['title'] = $languagetext['delete']['title'];
$sites['loeschen']['password'] = true;

$sites['login']['site'] = 'screens/login.php';
$sites['login']['title'] = $languagetext['login']['title'];
$sites['login']['password'] = false;

$sites['default']['site'] = 'screens/info.php';
$sites['default']['title'] = 'Info';
$sites['default']['password'] = false;