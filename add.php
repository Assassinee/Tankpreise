<?php
session_start();
require_once('config.php');

if(isset($_POST['submit'])) {

    if($_SESSION['eingeloggt'] == true) {

        $id = $_POST['tankstellenid'];

        $farbe = $diagramm['linienfarbe'][rand(0, count($diagramm['linienfarbe']) - 1)];

        $json = file_get_contents("https://creativecommons.tankerkoenig.de/json/detail.php?id=$id&apikey=$apiKey[tankerkoenig]");

        $data = json_decode($json, true);

        $name = $data['station']['name'];

        $beschreibung = 'hinzugefügt: ' . date('j-n-Y G:i');

        $mysqli = new mysqli($dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass'], $dbConfig['Database']);

        $sql = "insert into tankstellen (TankstellenID, Name, Farbe, Beschreibung) Values ('$id', '$name', '$farbe', '$beschreibung');";

        $abfrage = $mysqli->query($sql);

        if(!$abfrage) {

            $_SESSION['Fehler']['Titel'] = 'MYSQL Fehler';
            $_SESSION['Fehler']['Meldung'] = $mysqli->error;
        }

        header('location: index.php?site=Einstellung');
    } else {

        header('location: index.php?site=Einstellung');
    }
}