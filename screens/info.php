<?php

require_once 'config.php';

//DB
try {

    $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
} catch (PDOException $e) {

    echo $e->getMessage();
    exit();
}

//TODO: Limit variable machen
$sql = "Select min($BENZINART) From (SELECT $BENZINART FROM preise where Status = 'open' order by Zeit desc limit 5) as letztepreise";

$kommando = $db->query($sql);

$aktuellerpreis = $kommando->fetch()[0];

$sql = "SELECT min($BENZINART) FROM preise where Zeit >= :zeit and Status = 'open'";

$kommando = $db->prepare($sql);

$letzterTag = strtotime("today", time());
$zeit = date('o-n-j H:i:s', $letzterTag - 60 * 60 * 24 * 7);
$kommando->bindParam(':zeit', $zeit);

$kommando->execute();

$preisWoche = $kommando->fetch()[0];

$tabelle = '<table id="tabelleinfos"><tr><td>Aktueller Preis:</td><td>';
$tabelle .= $aktuellerpreis;
$tabelle .= '€</td></tr><tr><td>Preis 7 Tage:</td><td>';
$tabelle .= $preisWoche;
$tabelle .= '€</td></tr></table>';

echo $tabelle;
?>