<?php

if(isset($_POST['submitsuche']))// ausgewaehlte Tankstellen werden hinzugefuegt
{
    if(isset($_POST['tankstellenid']))
    {
        try
        {
            $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
        } catch (PDOException $e) {

            echo $e->getMessage();
            exit();
        }

        $sql = '';

        //TODO: add to service
       foreach ($_POST['tankstellenid'] as $key => $value)
        {
            $color = $diagramm['linienfarbe'][rand(0, count($diagramm['linienfarbe']) - 1)];

            $json = file_get_contents("https://creativecommons.tankerkoenig.de/json/detail.php?id=$value&apikey=$apiKey[tankerkoenig]");

            $data = json_decode($json, true);

            $name = $data['station']['name'];

            $beschreibung = 'hinzugefügt: ' . date('j-n-Y G:i');

            $sql .= "insert into tankstellen (TankstellenID, Name, Farbe, Beschreibung) Values ('$value', '$name', '$farbe', '$beschreibung');";
        }

        $command = $db->query($sql);

       if($error = $command->errorInfo()[0] == 0)
       {

       } else {
           $_SESSION['Fehler']['Titel'] = $languagetext['search'][12];
           $_SESSION['Fehler']['Meldung'] = $mysqli->error;
       }

        //sleep(1);

        header('location: index.php?site=Einstellung');
    } else {

        $_SESSION['Fehler']['Titel'] = $languagetext['search'][13];
        $_SESSION['Fehler']['Meldung'] = $languagetext['search'][14];
        header('location: index.php?site=Einstellung');
    }
}
elseif(isset($_POST['submit']))//Seite mit Karte & Tankstellen wird angezeigt
{
    //include
    require_once 'services/Services.php';

    //Post
    $address = $_POST['adresse'];
    $city = $_POST['stadt'];
    $postcode = $_POST['plz'];
    $radius = $_POST['radius'];

    //Variablen
    $addresstogether = $address . ', ' . $postcode . ' ' . $city;
    $address = str_replace(' ', '%20', $address);

    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $domain = substr($url, 0, strripos($url, '/'));

    //DB
    try
    {
        $db = new PDO('mysql:dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
    } catch (PDOException $e) {

        echo $e->getMessage();
        exit();
    }

    $sql = "SELECT TankstellenID From tankstellen";

    $command = $db->query($sql);

    $gasStations = Array();

    foreach ($command as $key => $value)
    {
        $gasStations[] = $value['TankstellenID'];
    }

    //Geocoding
    $geocoding = $servicesGeocoding[$services['Geocoding']];

    $geocoding->setAddress($address, $city, $postcode);

    $geocoding->calculateCoordinates();

    //Preise
    $tankpreise = $servicesPrices[$services['Prices']];

    $tankpreise->setData($geocoding->getLat(), $geocoding->getLng(), $radius);

    $data = $tankpreise->getStations();

    //Tabelle erstellen
    $tabelle = '<div style="margin-left: auto; margin-right: auto; width: 70%;">
                <form action="' . $_SERVER['REQUEST_URI'] . '" method = "POST" target="_self" accept-charset="UTF-8">
                <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark"><tr><th>'.$languagetext['search'][6].'</th><th>'.$languagetext['search'][7].'</th><th>'.$languagetext['search'][8].'</th><th>'.$languagetext['search'][9].'</th><th>'.$languagetext['search'][10].'</th><th>'.$languagetext['search'][11].'</th></tr></thead>';

    $map = $servicesMap[$services['Map']];

    $map->setData($geocoding->getLat(), $geocoding->getLng(), $addresstogether);

    $i = 1;
    foreach ($data as $key => $value)
    {
        if (in_array($value['id'], $gasStations))
        {
            $add = "bereits vorhanden";
            $picture = 'http://' . $domain . '/bilder/saule_vorhanden.png';
        } else {

            $add = "<input type='checkbox' name='tankstellenid[]' value='$value[id]'";
            $picture = 'http://' . $domain . '/bilder/saule.png';
        }

        $tabelle .= '<tr><td>' . $i . '</td><td>' . $value['id'] . '</td><td>' . $value['name']
            . '</td><td>' . $value['adresse'] . ' </td><td>' . $value['entfernung'] . 'km</td><td>'
            . $add . '</td></tr>';

        $map->addMarker($value['lat'], $value['lng'], $i . ':' . $value['name'] . ':' . $value['adresse'], $picture);

        $i++;
    }

    $tabelle .= '</table><button type="submit" name="submitsuche" class="btn btn-primary">'.$languagetext['search'][11].'</button></form></div>';

    echo $map->getMap();
    echo $tabelle;
    echo $map->getJS();

} else {

    echo '<div style="margin-left: auto; margin-right: auto; width: 50%; margin-top: 20px;">
            <form action="' . $_SERVER['REQUEST_URI'] . '" method = "POST" target="_self" accept-charset="UTF-8">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputAddress">'.$languagetext['search'][1].'</label>
                        <input type="text" name="adresse" class="form-control" id="inputAddress">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputCity">'.$languagetext['search'][2].'</label>
                        <input type="text" name="stadt" class="form-control" id="inputCity">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputZip">'.$languagetext['search'][3].'</label>
                        <input type="number" name="plz" class="form-control" id="inputZip">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputState">'.$languagetext['search'][4].'</label>
                        <select id="inputState" name="radius" class="form-control">
                            <option value="1">1 km</option>
                            <option value="2">2 km</option>
                            <option value="5" selected>5 km</option>
                            <option value="10">10 km</option>
                            <option value="25">25 km</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">'.$languagetext['search'][5].'</button>
            </form>
        </div>';
}
