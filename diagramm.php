<?php
$mysqli = new mysqli($dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass'], $dbConfig['Database']);

$aktuelleStunde = date('H');
$tageVormonat = date('t', time() - ((date('j') + 1) * 86400));;
$jahr = date('Y');
$monat = date('n');
$tag = date('j');

$sql = "Select TankstellenID, Name, Farbe From tankstellen";

$result = $mysqli->query($sql);

$row = $result->fetch_row();

$tankstellen = Array();
$tankstellenFarbe = Array();

while ($row != null) {

    $tankstellen[$row[0]] = $row[1];
    $tankstellenFarbe[$row[0]] = $row[2];
    $row = $result->fetch_row();
}

$datenset = '';

$labels = "'" . (($aktuelleStunde - 6) < 0 ? $aktuelleStunde - 6 + 24 : $aktuelleStunde - 6) . " Uhr', '" .
    (($aktuelleStunde - 5) < 0 ? $aktuelleStunde - 5 + 24 : $aktuelleStunde - 5) . " Uhr', '" .
    (($aktuelleStunde - 4) < 0 ? $aktuelleStunde - 4 + 24 : $aktuelleStunde - 4) . " Uhr', '" .
    (($aktuelleStunde - 3) < 0 ? $aktuelleStunde - 3 + 24 : $aktuelleStunde - 3) . " Uhr', '" .
    (($aktuelleStunde - 2) < 0 ? $aktuelleStunde - 2 + 24 : $aktuelleStunde - 2) . " Uhr', '" .
    (($aktuelleStunde - 1) < 0 ? $aktuelleStunde - 1 + 24 : $aktuelleStunde - 1) . " Uhr', '" .
    $aktuelleStunde . " Uhr'";

foreach ($tankstellen as $key => $value) {

    $preise = '';

    for($i = 6; $i >= 0; $i--) {

        $abfrageStunde = ($aktuelleStunde - ($i + 1)) < 0 ? ($aktuelleStunde + 24) - ($i + 1) : $aktuelleStunde - ($i + 1);
        $abfrageTag = ($aktuelleStunde - ($i + 1)) < 0 ? ($tag - 1) < 1 ? $tageVormonat : $tag - 1 : $tag;
        $abfrageMonat = ($tag - 1) < 1 ? ($monat - 1) < 1 ? 12 : $monat : $monat;
        $abfrageJahr = ($monat - 1) < 1 ? ($jahr - 1) : $jahr;

        $von = "$abfrageJahr-$abfrageMonat-$abfrageTag $abfrageStunde:00:00";
        $bis = "$abfrageJahr-$abfrageMonat-$abfrageTag $abfrageStunde:59:59";

        $benzinart = $diagramm['benzinart'];

        $sql = "SELECT min($benzinart)
        From preise
        Where TankstellenID = '$key' and Status = 'open'
        And Zeit between '$von' and '$bis'";

        $result = $mysqli->query($sql);

        $row = $result->fetch_row();

        $aktuellerpreis = $row[0];

        $preise .= $aktuellerpreis . ',';
    }

    $preise = substr($preise, 0, -1);

    $datenset .= "{ label: '$value',
        backgroundColor: 'rgba($tankstellenFarbe[$key],$diagramm[Farbstaerkeflaeche])',
        borderColor: 'rgba($tankstellenFarbe[$key],$diagramm[FarbstaerkeLinie])',
        data: [$preise]
    },";
}
$datenset = substr($datenset, 0, -1);
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
            }
        });
    </script>
</div>