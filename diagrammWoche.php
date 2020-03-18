<?php
$letzterTag = strtotime("today", time());

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

$startzeit = $letzterTag - 7 * 24 * 60 * 60;
$preiszeiten = array();

foreach ($tankstellen as &$tk) {

    $sql = 'SELECT Zeit, ' . $BENZINART . ' From preise where TankstellenID = :id and Status = "open" and Zeit >= :zeit';

    $kommando = $db->prepare($sql);

    $id = $tk['TankstellenID'];
    $kommando->bindParam(':id', $id);

    $zeit = date('o-n-j H:i:s', $startzeit);
    $kommando->bindParam(':zeit', $zeit);

    $kommando->execute();

    $preis = Array();

    while ($test = $kommando->fetch(PDO::FETCH_ASSOC)) {

        $preis[strtotime($test['Zeit'])] = $test[$BENZINART];
        $preiszeiten[strtotime($test['Zeit'])] = null;
    }
    $tk['Preise'] = $preis;
}

ksort($preiszeiten);

foreach ($tankstellen as $key => &$value)
{
    foreach ($preiszeiten as $key2 => $value2)
    {
        if (!key_exists($key2, $value['Preise']))
        {
            $value['Preise'][$key2] = null;
        }
    }
    ksort($value['Preise']);
}



$zeitsprung = 60 * 60 * 8;

foreach ($tankstellen as $key => &$value) {

    $preisezusammengefasst = Array();
    $anfangszeit = $startzeit;
    $endzeit = $startzeit + $zeitsprung;
    $preis = 0;

    foreach ($value['Preise'] as $key2 => $value2) {

        if ($key2 >= $anfangszeit && $key2 < $endzeit) {

            $preis = (($value2 < $preis) ? $value2 : (($preis == 0) ? $value2: $preis));
        } else {

            $preisezusammengefasst[$anfangszeit] = $preis;

            $preis = $value2;
            $anfangszeit = $endzeit;
            $endzeit += $zeitsprung;
        }


    }

    $value['Preise'] = $preisezusammengefasst;


}

unset($key, $value);

foreach ($tankstellen[0]['Preise'] as $key => $value) {

    echo date('d.m.Y G:i:s', $key) . ' == ' . ($value == null ? 'NULL' : $value) . '<br/>';
}



unset($key, $value);





//$anzahlZusammen = 37; //eine Stunden => 6
//
//foreach ($tankstellen as $key => &$value) {
//
//    $preisezusammengefasst = Array();
//    $preis = 0;
//    $zeit = 0;
//    $anzahl = 0;
//
//    foreach ($value['Preise'] as $key2 => $value2) {
//
//        if ($value != NULL) {
//
//            $preis = $preis > $value2 ? $value2 : $preis == 0 ? $value2 : $preis;
//        }
//        $zeit = $zeit == 0 ? $key2 : $zeit;
//        $anzahl++;
//
//        if ($anzahl == $anzahlZusammen) {
//
//            $preisezusammengefasst[$zeit] = $preis;
//            $preis = 0;
//            $zeit = 0;
//            $anzahl = 0;
//        }
//    }
//    $value['Preise'] = $preisezusammengefasst;
//}




$tankpreise = Array();
unset($key, $value);

foreach ($tankstellen as $key => $value)
{
    foreach ($value['Preise'] as $key2 => $value2)
    {
        $tankpreise[$key2][$value['TankstellenID']] = $value2;
    }
}





ksort($tankpreise);


//print_r($tankpreise);



/*
$anzahlZusammen = 12; //zwei Stunden

foreach ($tankstellen as $key => $value)
{
    $preisezusammengefasst = Array();
    $anzahl = 0;
    $tempPreis = 0;
    $tempTime = 0;

    foreach ($value['Preise'] as $key2 => $value2)
    {
        if ($anzahl == $anzahlZusammen)
        {
            $preisezusammengefasst[$tempTime] = round(($tempPreis / $anzahl), 3);
            $tempTime = 0;
            $tempPreis = 0;
            $anzahl = 0;
        }

        $tempPreis += $value2;
        $anzahl++;
        $tempTime = $tempTime == 0 ? $key2 : $tempTime;
    }
}
*/



unset($key, $value);

$datenset = "";



foreach ($tankstellen as $tanke)
{
    $preisString = '';

    foreach ($tankpreise as $zeitpunkt) {
        $test = null;
        foreach ($zeitpunkt as $key => $value) {
            if ($key == $tanke['TankstellenID']) {
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
    $temp = date('d.m.Y G:i', $preise);
    $labels .= "'$temp',";
}
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