<?php

$mysqli = new mysqli($dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass'], $dbConfig['Database']);

$sql = 'SELECT count(TankstellenID) FROM tankstellen';

$result = $mysqli->query($sql);

$anzahl = $result->fetch_row();

$anzahl = $anzahl[0];

$benzinart = $diagramm['benzinart'];

$sql = "SELECT $benzinart FROM preise order by Zeit desc limit $anzahl";

$result = $mysqli->query($sql);

$row = $result->fetch_row();

$aktuellerpreis = 10.0;

while ($row != null) {

    if($row[0] != 0 && $row[0] < $aktuellerpreis) {

        $aktuellerpreis = $row[0];
    }
    $row = $result->fetch_row();
}

$tabelle = '<table id="tabelleinfos"><th>aktueller Preis:</th><td>';

$tabelle .= '<tr><td>' . $aktuellerpreis . '</td></tr>';

$anfang = date('Y-m-d G:i:s', (time() - (60 * 60 * 24 * 7)));
$ende = date('Y-m-d G:i:s');

$sql = "SELECT min($benzinart), max($benzinart) FROM preise where Zeit BETWEEN '$anfang' and '$ende' and Status = 'open'";

$result = $mysqli->query($sql);

$row = $result->fetch_row();

$tabelle .= "<tr><td>min Preis:</td></tr><tr><td>$row[0]</td></tr>";
$tabelle .= "<tr><td>max Preis:</td></tr><tr><td>$row[1]</td></tr>";





$tabelle .= '</td></table>';

echo $tabelle;


?>