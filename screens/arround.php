<?php
if(isset($_POST['submit'])) //Seite mit Karte & Tankstellen wird angezeigt
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

    //Geocoding
    $geocoding = $servicesGeocoding[$services['Geocoding']];

    $geocoding->setAddress($address, $city, $postcode);

    $geocoding->calculateCoordinates();

    //Preise
    $tankpreise = $servicesPrices[$services['Prices']];

    $tankpreise->setData($geocoding->getLat(), $geocoding->getLng(), $radius);

    $data = $tankpreise->getStations();

    //Tabelle erstellen
    $tabelle = '<div style="margin-left: auto; margin-right: auto; width: 100%;">
                <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark"><tr><th>'.$languagetext['arround']['nr'].'</th><th>'.$languagetext['arround']['id'].'</th><th>'.$languagetext['arround']['name'].'</th><th>'.$languagetext['arround']['street'].'</th><th>'.$languagetext['arround']['distance'].'</th><th>'.$languagetext['arround']['price'].'['.$BENZINART.']</th></tr></thead>';

    $map = $servicesMap[$services['Map']];

    $map->setData($geocoding->getLat(), $geocoding->getLng(), $addresstogether);

    usort($data, function($item1, $item2) use ($BENZINART) { return $item1[$BENZINART] <=> $item2[$BENZINART]; });

    $i = 1;
    foreach ($data as $key => $value)
    {
        if ($value['isOpen'] != null)
        {
            $picture = 'http://' . $domain . '/bilder/saule.png';

            $tabelle .= '<tr><td>' . $i . '</td><td>' . $value['id'] . '</td><td>' . $value['name']
                . '</td><td>' . $value['adresse'] . ' </td><td>' . $value['entfernung'] . 'km</td><td>' . ($value['E5'] != null ? $value['E5'] . '€' : '-') . '</td></tr>';
        }
        else
        {
            $picture = 'http://' . $domain . '/bilder/saule_geschlossen.png';

            $tabelle .= '<tr><td>' . $i . '</td><td>' . $value['id'] . '</td><td>' . $value['name']
                . '</td><td>' . $value['adresse'] . ' </td><td>' . $value['entfernung'] . 'km</td><td>' . $languagetext['arround']['closed'] . '</td></tr>';
        }
        $map->addMarker($value['lat'], $value['lng'], $i . ': ' . $value['name'] . ':' . $value['adresse'], $picture);

        $i++;
    }

    $tabelle .= '</table></div>';

    echo $map->getMap(100, 75);
    echo $tabelle;
    echo $map->getJS();
}
else
{
    echo '<div style="margin-left: auto; margin-right: auto; width: 50%; margin-top: 20px;">
            <form action="' . $_SERVER['REQUEST_URI'] . '" method = "POST" target="_self" accept-charset="UTF-8">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputAddress">'.$languagetext['arround']['address'].'</label>
                        <input type="text" name="adresse" class="form-control" id="inputAddress">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputCity">'.$languagetext['arround']['city'].'</label>
                        <input type="text" name="stadt" class="form-control" id="inputCity">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputZip">'.$languagetext['arround']['postcode'].'</label>
                        <input type="number" name="plz" class="form-control" id="inputZip">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputState">'.$languagetext['arround']['radius'].'</label>
                        <select id="inputState" name="radius" class="form-control">
                            <option value="1">1 km</option>
                            <option value="2">2 km</option>
                            <option value="5" selected>5 km</option>
                            <option value="10">10 km</option>
                            <option value="25">25 km</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">'.$languagetext['arround']['search'].'</button>
            </form>
        </div>';
}
