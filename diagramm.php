<?php

$letzteStunde = time() - (time() / 60 % 60) * 60 - (time() % 60);

//DB
try {

    $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
} catch (PDOException $e) {

    echo $e->getMessage();
    exit();
}

$sql = 'Select TankstellenID, Name, Farbe From tankstellen';

$ergebnis = $db->query($sql);

$tankstellen = Array();

foreach ($ergebnis as $zeile) {

    $tankstelle = Array();

    $tankstelle = [
            'TankstellenID' => $zeile['TankstellenID'],
            'Name' => $zeile['Name'],
            'Farbe' => $zeile['Farbe'],
            'Preise' => null
    ];

    $tankstellen[] = $tankstelle;
}

$startzeit = $letzteStunde - 6 * 60 * 60;

foreach ($tankstellen as &$tk) {

    $sql = 'SELECT Zeit, ' . $diagramm['benzinart'] . ' From preise where TankstellenID = :id and Status = "open" and Zeit >= :zeit';

    $kommando = $db->prepare($sql);

    $id = $tk['TankstellenID'];
    $kommando->bindParam(':id', $id);

    $zeit = date('o-n-j H:i:s', $startzeit);
    $kommando->bindParam(':zeit', $zeit);

    $kommando->execute();

    $preis2 = Array();

    while ($test = $kommando->fetch(PDO::FETCH_ASSOC)) {

        $preis2[strtotime($test['Zeit'])] = $test['E5'];
    }

    $tk['Preise'] = $preis2;
}


unset($tk);


$tankpreise = Array();

foreach ($tankstellen as $tk)
{
    foreach ($tk['Preise'] as $key => $value)
    {
        $tankpreise[$key][$tk['TankstellenID']] = $value;
    }

}

ksort($tankpreise);

//print_r($tankpreise);
unset($key, $value);

$datenset = "";

foreach ($tankstellen as $tanke)
{
    $preisString = '';

    foreach ($tankpreise as $zeitpunkt)
    {
        $test = null;
        foreach ($zeitpunkt as $key => $value)
        {
            if ($key == $tanke['TankstellenID'])
            {
                $test = $value;
            }

        }
        $preisString .= $test == null ? null : $test;
        $preisString .= ',';



    }

    $preisString = substr($preisString, 0, -1);


    $datenset .= "{ label: '$tanke[Name]',
        backgroundColor: 'rgba($tanke[Farbe],$diagramm[Farbstaerkeflaeche])',
        borderColor: 'rgba($tanke[Farbe],$diagramm[FarbstaerkeLinie])',
        data: [$preisString]
    },";


}






$labels = '';

foreach ($tankpreise as $preise => $value)
{
    $temp = date('G:i', $preise);
    $labels .= "'$temp',";




}




//foreach ($tankstellen as $tk)
//{
//    $preise = null;
//
//    foreach ($tk['Preise'] as $preis)
//    {
//        $preise .= $preis . ',';
//    }
//    $preise = substr($preise, 0, -1);
//
//
//
//    $datenset .= "{ label: '$tk[Name]',
//        backgroundColor: 'rgba($tk[Farbe],$diagramm[Farbstaerkeflaeche])',
//        borderColor: 'rgba($tk[Farbe],$diagramm[FarbstaerkeLinie])',
//        data: [$preise]
//    },";
//}
//$datenset = substr($datenset, 0, -1);


//$labels = "'" . date('H', $letzteStunde - 60 * 60 * 5) . " Uhr', '" .
//    date('H', $letzteStunde - 60 * 60 * 4) . " Uhr', '" .
//    date('H', $letzteStunde - 60 * 60 * 3) . " Uhr', '" .
//    date('H', $letzteStunde - 60 * 60 * 2) . " Uhr', '" .
//    date('H', $letzteStunde - 60 * 60) . " Uhr', '" .
//    date('H', $letzteStunde) . " Uhr', '" .
//    date('H:i', time()) . " Uhr'"
//    . ',,,,,,,,,,,,,,,,,,,,,,,,';


















//$mysqli = new mysqli($dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass'], $dbConfig['Database']);
//
//$aktuelleStunde = date('H');
//$tageVormonat = date('t', time() - ((date('j') + 1) * 86400));;
//$jahr = date('Y');
//$monat = date('n');
//$tag = date('j');
//
//$sql = "Select TankstellenID, Name, Farbe From tankstellen";
//
//$result = $mysqli->query($sql);
//
//$row = $result->fetch_row();
//
//$tankstellen = Array();
//$tankstellenFarbe = Array();
//
//while ($row != null) {
//
//    $tankstellen[$row[0]] = $row[1];
//    $tankstellenFarbe[$row[0]] = $row[2];
//    $row = $result->fetch_row();
//}
//
//$datenset = '';
//
//$labels = "'" . (($aktuelleStunde - 6) < 0 ? $aktuelleStunde - 6 + 24 : $aktuelleStunde - 6) . " Uhr', '" .
//    (($aktuelleStunde - 5) < 0 ? $aktuelleStunde - 5 + 24 : $aktuelleStunde - 5) . " Uhr', '" .
//    (($aktuelleStunde - 4) < 0 ? $aktuelleStunde - 4 + 24 : $aktuelleStunde - 4) . " Uhr', '" .
//    (($aktuelleStunde - 3) < 0 ? $aktuelleStunde - 3 + 24 : $aktuelleStunde - 3) . " Uhr', '" .
//    (($aktuelleStunde - 2) < 0 ? $aktuelleStunde - 2 + 24 : $aktuelleStunde - 2) . " Uhr', '" .
//    (($aktuelleStunde - 1) < 0 ? $aktuelleStunde - 1 + 24 : $aktuelleStunde - 1) . " Uhr', '" .
//    $aktuelleStunde . " Uhr'";
//
//foreach ($tankstellen as $key => $value) {
//
//    $preise = '';
//
//    for($i = 6; $i >= 0; $i--) {
//
//        $abfrageStunde = ($aktuelleStunde - ($i + 1)) < 0 ? ($aktuelleStunde + 24) - ($i + 1) : $aktuelleStunde - ($i + 1);
//        $abfrageTag = ($aktuelleStunde - ($i + 1)) < 0 ? ($tag - 1) < 1 ? $tageVormonat : $tag - 1 : $tag;
//        $abfrageMonat = ($tag - 1) < 1 ? ($monat - 1) < 1 ? 12 : $monat : $monat;
//        $abfrageJahr = ($monat - 1) < 1 ? ($jahr - 1) : $jahr;
//
//        $von = "$abfrageJahr-$abfrageMonat-$abfrageTag $abfrageStunde:00:00";
//        $bis = "$abfrageJahr-$abfrageMonat-$abfrageTag $abfrageStunde:59:59";
//
//        $benzinart = $diagramm['benzinart'];
//
//        $sql = "SELECT min($benzinart)
//        From preise
//        Where TankstellenID = '$key' and Status = 'open'
//        And Zeit between '$von' and '$bis'";
//
//        $result = $mysqli->query($sql);
//
//        $row = $result->fetch_row();
//
//        $aktuellerpreis = $row[0];
//
//        $preise .= $aktuellerpreis . ',';
//    }
//
//    $preise = substr($preise, 0, -1);
//
//    $datenset .= "{ label: '$value',
//        backgroundColor: 'rgba($tankstellenFarbe[$key],$diagramm[Farbstaerkeflaeche])',
//        borderColor: 'rgba($tankstellenFarbe[$key],$diagramm[FarbstaerkeLinie])',
//        data: [$preise]
//    },";
//}
//$datenset = substr($datenset, 0, -1);
?>
<div class="Diagramm">
    <canvas id="myChart"></canvas>
    <script src="js/Chart.js"></script>
    <script>
        var myChartObject = document.getElementById('myChart');

        var chart = new Chart(myChartObject, {
            type: 'line',
            data: {
                labels: [<?php echo $labels; ?>],
                datasets: [<?php echo $datenset; ?>]
            },
            options: {
                animation: {
                    duration: 2000
                }
            }
        });
    </script>
</div>