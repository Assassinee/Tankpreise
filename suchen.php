<?php
if($_SESSION['eingeloggt'] == true) {

    if(isset($_POST['submitsuche'])) {

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
    elseif(isset($_POST['submit'])) {

        $mysqli = new mysqli($dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass'], $dbConfig['Database']);

        $sql = "SELECT TankstellenID From tankstellen";

        $result = $mysqli->query($sql);

        $row = $result->fetch_all();

        $tankstellenVorhanden = Array();

        for ($i = 1; $i < sizeof($row); $i++) {

            $tankstellenVorhanden[] = $row[$i][0];
        }

        $adresse = $_POST['adresse'];
        $stadt = $_POST['stadt'];
        $plz = $_POST['plz'];
        $radius = $_POST['radius'];

        $adressezusammen = $adresse . ', ' . $plz . ' ' . $stadt;
        $adresse = str_replace(' ', '%20', $adresse);
        $icon = '';

        //Geocoding
        $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$adresse,$plz,$stadt&key=$apiKey[geocoding]");

        $data = json_decode($json, true);

        $lat = $data['results'][0]['geometry']['location']['lat'];
        $lng = $data['results'][0]['geometry']['location']['lng'];

        //Tabelle
        $json = file_get_contents('https://creativecommons.tankerkoenig.de/json/list.php'
            ."?lat=$lat"
            ."&lng=$lng"
            ."&rad=$radius"
            ."&sort=dist"
            ."&type=all"
            ."&apikey=$apiKey[tankerkoenig]");

        $data = json_decode($json, true);

        $tabelle = '<div style="margin-left: auto; margin-right: auto; width: 70%;">
                    <form action="' . $_SERVER['REQUEST_URI'] . '" method = "POST" target="_self" accept-charset="UTF-8">
                    <table class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark"><tr><th>NR</th><th>TankstellenID</th><th>Name</th><th>Straße</th><th>Entfernung</th><th>hinzufügen</th></tr></thead>';
        $i = 1;
        $markers = '';

        foreach ($data['stations'] as $key => $value) {

            $tankstellenadresse = $value['street'] . (($value['houseNumber'] != '') ? ' ' . $value['houseNumber'] : '') . ', ' . $value['place'];

            $name = $_SERVER['SERVER_NAME'];
            $pfad = $_SERVER['REQUEST_URI'];

            $domain = $name . $pfad;
            $ausgabe2 = substr($domain, 0, strripos($domain, '/'));

            if (in_array($value['id'], $tankstellenVorhanden)) {

                $hinzufuegen = "bereits vorhanden";
                $saule = 'https://' . $ausgabe2 . '/bilder/saule_vorhanden.png';
            } else {

                $hinzufuegen = "<input type='checkbox' name='tankstellenid[]' value='$value[id]'";
                $saule = 'https://' . $ausgabe2 . '/bilder/saule.png';
            }

            $tabelle .= "<tr><td>$i</td><td>$value[id]</td><td>$value[name]</td><td>$tankstellenadresse</td><td>$value[dist]km</td><td>$hinzufuegen</td></tr>";

            $markers .= "new google.maps.Marker({position: {lat: $value[lat], lng: $value[lng]}, map: map, title: '$i: $value[name]: $tankstellenadresse', icon: '$saule'});";
            $i++;
        }

        $tabelle .= '</table><button type="submit" name="submitsuche" class="btn btn-primary">hinzufügen</button></form></div>';

        $map = "<div style='margin-left: auto; margin-right: auto; height: 50%; width: 70%;'><div style='height: 100%;' id=\"map\"></div></div>";
        $jsMap = "<script>
                    function initMap() {
                      
                        var map = new google.maps.Map(document.getElementById('map'), {
                            center: {lat: $lat, lng: $lng},
                            zoom: 15
                        });
                    
                        new google.maps.Marker({
                            position: {lat: $lat, lng: $lng},
                            map: map,
                            title: 'Angabe: $adressezusammen'
                        });
                        $markers;
                    }
                  </script>
                  <script src=\"https://maps.googleapis.com/maps/api/js?key=$apiKey[mapsjavascript]&callback=initMap\" async defer></script>";

        echo $map;
        echo $tabelle;
        echo $jsMap;

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