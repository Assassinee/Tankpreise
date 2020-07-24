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
           $_SESSION['Fehler']['Titel'] = $languagetext['search']['mysqlerror'];
           $_SESSION['Fehler']['Meldung'] = $mysqli->error;
       }

        //sleep(1);

        header('location: index.php?site=Einstellung');
    } else {

        $_SESSION['Fehler']['Titel'] = $languagetext['search']['error'];
        $_SESSION['Fehler']['Meldung'] = $languagetext['search']['errormsg'];
        header('location: index.php?site=Einstellung');
    }
}
elseif(isset($_POST['submit']) || (isset($_GET['lat']) && isset($_GET['lng']) && isset($_GET['radius'])))//Seite mit Karte & Tankstellen wird angezeigt
{
    //include
    require_once 'services/Services.php';

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

    if (isset($_GET['lat']) && isset($_GET['lng']))
    {
        $lat = $_GET['lat'];
        $lng = $_GET['lng'];
        $radius = $_GET['radius'];

        $addresstogether = $lat . ',' . $lng;
    }
    else
    {
        //Post
        $address = $_POST['adresse'];
        $city = $_POST['stadt'];
        $postcode = $_POST['plz'];
        $radius = $_POST['radius'];

        //Variablen
        $addresstogether = $address . ', ' . $postcode . ' ' . $city;
        $address = str_replace(' ', '%20', $address);

        //Geocoding
        $geocoding = $servicesGeocoding[$services['Geocoding']];

        $geocoding->setAddress($address, $city, $postcode);

        $geocoding->calculateCoordinates();

        $lat = $geocoding->getLat();
        $lng = $geocoding->getLng();
    }

    //Preise
    $tankpreise = $servicesPrices[$services['Prices']];

    $tankpreise->setData($lat, $lng, $radius);

    $data = $tankpreise->getStations();

    //Tabelle erstellen
    $tabelle = '<div style="margin-left: auto; margin-right: auto; width: 100%;">
                <form action="' . $_SERVER['REQUEST_URI'] . '" method = "POST" target="_self" accept-charset="UTF-8">
                <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark"><tr><th>'.$languagetext['search']['nr'].'</th><th>'.$languagetext['search']['id'].'</th><th>'.$languagetext['search']['name'].'</th><th>'.$languagetext['search']['street'].'</th><th>'.$languagetext['search']['distance'].'</th><th>'.$languagetext['search']['add'].'</th></tr></thead>';

    $map = $servicesMap[$services['Map']];

    $map->setData($lat, $lng, $addresstogether);

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

        $map->addMarker($value['lat'], $value['lng'], $i . ': ' . $value['name'] . ':' . $value['adresse'], $picture);

        $i++;
    }

    $tabelle .= '</table><button type="submit" name="submitsuche" class="btn btn-primary">'.$languagetext['search']['add'].'</button></form></div>';

    echo $map->getMap(100, 70);
    echo $tabelle;
    echo $map->getJS();

} else {

    echo '<div style="margin-left: auto; margin-right: auto; width: 50%; margin-top: 20px;">
            <form action="' . $_SERVER['REQUEST_URI'] . '" method = "POST" target="_self" accept-charset="UTF-8">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputAddress">'.$languagetext['search']['address'].'</label>
                        <input type="text" name="adresse" class="form-control" id="inputAddress">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputCity">'.$languagetext['search']['city'].'</label>
                        <input type="text" name="stadt" class="form-control" id="inputCity">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputZip">'.$languagetext['search']['postcode'].'</label>
                        <input type="number" name="plz" class="form-control" id="inputZip">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputState">'.$languagetext['search']['radius'].'</label>
                        <select id="inputState" name="radius" class="form-control">
                            <option value="1">1 km</option>
                            <option value="2">2 km</option>
                            <option value="5" selected>5 km</option>
                            <option value="10">10 km</option>
                            <option value="25">25 km</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">'.$languagetext['search']['search'].'</button>
                <button type="button" onclick="locationsearch()" class="btn btn-warning">'.$languagetext['search']['location'].'</button>
            </form>
        </div>
        <script src="js/location.js"></script>';
}
