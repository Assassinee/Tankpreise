<?php
if($_SESSION['eingeloggt'] == true || true) {

    if(isset($_POST['submitsuche'])) {  // ausgewaehlte Tankstellen werden hinzugefuegt

        if(isset($_POST['tankstellenid'])) {

            $mysqli = new mysqli($dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass'], $dbConfig['Database']);

            $sql = '';

            foreach ($_POST['tankstellenid'] as $key => $value) {

                $farbe = $diagramm['linienfarbe'][rand(0, count($diagramm['linienfarbe']) - 1)];

                $json = file_get_contents("https://creativecommons.tankerkoenig.de/json/detail.php?id=$value&apikey=$apiKey[tankerkoenig]");

                $data = json_decode($json, true);

                $name = $data['station']['name'];

                $beschreibung = 'hinzugefügt: ' . date('j-n-Y G:i');

                $sql .= "insert into tankstellen (TankstellenID, Name, Farbe, Beschreibung) Values ('$value', '$name', '$farbe', '$beschreibung');";
            }

            $abfrage = $mysqli->multi_query($sql);

            if(!$abfrage) {

                $_SESSION['Fehler']['Titel'] = 'MYSQL Fehler';
                $_SESSION['Fehler']['Meldung'] = $mysqli->error;
            }

            sleep(1);

            header('location: index.php?site=Einstellung');
        } else {

            $_SESSION['Fehler']['Titel'] = 'Fehler';
            $_SESSION['Fehler']['Meldung'] = 'Es wurden keine Tankstellen ausgewählt';
            header('location: index.php?site=Einstellung');
        }
    }
    elseif(isset($_POST['submit'])) {   //Seite mit Karte & Tankstellen wird angezeigt

        //include
        require_once 'services/Services.php';

        //Post
        $adresse = $_POST['adresse'];
        $stadt = $_POST['stadt'];
        $plz = $_POST['plz'];
        $radius = $_POST['radius'];

        //Variablen
        $adressezusammen = $adresse . ', ' . $plz . ' ' . $stadt;
        $adresse = str_replace(' ', '%20', $adresse);

        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $domain = substr($url, 0, strripos($url, '/'));

        //DB
        try {

            $db = new PDO('mysql:dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
        } catch (PDOException $e) {

            echo $e->getMessage();
            exit();
        }

        $sql = "SELECT TankstellenID From tankstellen";

        $ergebnis = $db->query($sql);

        $tankstellenVorhanden = Array();

        foreach ($ergebnis as $zeile) {

            $tankstellenVorhanden[] = $zeile['TankstellenID'];
        }

        //Geocoding
        $geocoding = $servicesGeocoding[$services['Geocoding']];

        $geocoding->setAddress($adresse, $stadt, $plz);

        $geocoding->calculateCoordinates();

        //Preise
        $tankpreise = $servicesPrices[$services['Prices']];

        $tankpreise->setData($geocoding->getLat(), $geocoding->getLng(), $radius);

        $data = $tankpreise->getStations();

        //Tabelle erstellen
        $tabelle = '<div style="margin-left: auto; margin-right: auto; width: 70%;">
                    <form action="' . $_SERVER['REQUEST_URI'] . '" method = "POST" target="_self" accept-charset="UTF-8">
                    <table class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark"><tr><th>NR</th><th>TankstellenID</th><th>Name</th><th>Straße</th><th>Entfernung</th><th>hinzufügen</th></tr></thead>';

        $map = $servicesMap[$services['Map']];

        $map->setData($geocoding->getLat(), $geocoding->getLng(), $adressezusammen);

        $i = 1;
        foreach ($data as $tankstelle)
        {
            if (in_array($tankstelle['id'], $tankstellenVorhanden)) {

                $hinzufuegen = "bereits vorhanden";
                $saule = 'http://' . $domain . '/bilder/saule_vorhanden.png';
            } else {

                $hinzufuegen = "<input type='checkbox' name='tankstellenid[]' value='$tankstelle[id]'";
                $saule = 'http://' . $domain . '/bilder/saule.png';
            }

            $tabelle .= '<tr><td>' . $i . '</td><td>' . $tankstelle['id'] . '</td><td>' . $tankstelle['name']
                . '</td><td>' . $tankstelle['adresse'] . ' </td><td>' . $tankstelle['entfernung'] . 'km</td><td>'
                . $hinzufuegen . '</td></tr>';

            $map->addMarker($tankstelle['lat'], $tankstelle['lng'], $i . ':' . $tankstelle['name'] . ':' . $tankstelle['adresse'], $saule);

            $i++;
        }

        $tabelle .= '</table><button type="submit" name="submitsuche" class="btn btn-primary">hinzufügen</button></form></div>';

        echo $map->getMap();
        echo $tabelle;
        echo $map->getJS();

    } else {

        echo '<div style="margin-left: auto; margin-right: auto; width: 50%; margin-top: 20px;">
                <form action="' . $_SERVER['REQUEST_URI'] . '" method = "POST" target="_self" accept-charset="UTF-8">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="inputAddress">Adresse</label>
                            <input type="text" name="adresse" class="form-control" id="inputAddress">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="inputCity">Stadt</label>
                            <input type="text" name="stadt" class="form-control" id="inputCity">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="inputZip">Postleitzahl</label>
                            <input type="number" name="plz" class="form-control" id="inputZip">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="inputState">Umkreis</label>
                            <select id="inputState" name="radius" class="form-control">
                                <option value="1">1 KM</option>
                                <option value="2">2 KM</option>
                                <option value="5" selected>5 KM</option>
                                <option value="10">10 KM</option>
                                <option value="25">25 KM</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">suchen</button>
                </form>
            </div>';
    }
} else {
    require_once('login.php');
}