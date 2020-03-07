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




$anzahlZusammen = 12; //eine Stunden => 6

foreach ($tankstellen as $key => &$value)
{
    $preisezusammengefasst = Array();
    $anzahl = 0;
    $preis = 0;
    $zeit = 0;
    $anzahlkomplett = 0;

    foreach ($value['Preise'] as $key2 => $value2)
    {
        if ($value2 != NULL) {

            $preis += $value2;
            $anzahl++;
        }
        $zeit = $zeit == 0 ? $key2 : $zeit;
        $anzahlkomplett++;

        if ($anzahlkomplett == $anzahlZusammen) {

            if ($anzahl != 0) {

                $preisezusammengefasst[$zeit] = round(($preis / $anzahl), 3);
            }

            $zeit = 0;
            $preis = 0;
            $anzahl = 0;
            $anzahlkomplett = 0;
        }
    }


    $value['Preise'] = $preisezusammengefasst;
    //print_r($preisezusammengefasst);
}



//print_r($tankstellen);




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