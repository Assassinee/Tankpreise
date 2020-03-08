<?php

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

foreach ($tankstellen as &$tk) {

    $sql = 'SELECT Zeit, ' . $BENZINART . ' From preise where TankstellenID = :id and Status = "open" and Zeit >= :zeit';

    $kommando = $db->prepare($sql);

    $id = $tk['TankstellenID'];
    $kommando->bindParam(':id', $id);

    $lastcomphour = time() - (time() % 3600);
    $abfragezeit = $lastcomphour - (6 * 60 * 60);
    $zeit = date('o-n-j H:i:s', $abfragezeit);
    $kommando->bindParam(':zeit', $zeit);

    $kommando->execute();

    $preis = Array();

    while ($test = $kommando->fetch(PDO::FETCH_ASSOC)) {

        $preis[strtotime($test['Zeit'])] = $test[$BENZINART];
    }

    $tk['Preise'] = $preis;
}

unset($tk);

$tankstellenpreise = Array();

foreach ($tankstellen as $tk)
{
    foreach ($tk['Preise'] as $key => $value)
    {
        $tankstellenpreise[$key][$tk['TankstellenID']] = $value;
    }
}

ksort($tankstellenpreise);

unset($key, $value);

$datenset = "";

foreach ($tankstellen as $tanke)
{
    $preisString = '';

    foreach ($tankstellenpreise as $zeitpunkt) {
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

foreach ($tankstellenpreise as $preise => $value)
{
    $temp = date('G:i', $preise);
    $labels .= "'$temp',";
}
$labels = substr($labels, 0, -1);
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