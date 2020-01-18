<?php
$mysqli = new mysqli($dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass'], $dbConfig['Database']);

$aktuellerTag = date('N');
$tageVormonat = date('t', time() - ((date('j') + 1) * 86400));

$jahr = date('Y');
$monat = date('n');
$tag = date('j');

function tagumwandeln($tag) {

    $ausgeschrieben = '';

    switch($tag) {
        case 1:
            $ausgeschrieben = 'Montag';
            break;
        case 2:
            $ausgeschrieben = 'Dienstag';
            break;
        case 3:
            $ausgeschrieben = 'Mittwoch';
            break;
        case 4:
            $ausgeschrieben = 'Donnerstag';
            break;
        case 5:
            $ausgeschrieben = 'Freitag';
            break;
        case 6:
            $ausgeschrieben = 'Samstag';
            break;
        case 7:
            $ausgeschrieben = 'Sonntag';
            break;
        default:
            $ausgeschrieben = '';
            break;
    }
    return $ausgeschrieben;
}

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

$labels = "'" . tagumwandeln((($aktuellerTag - 6) < 0) ? ($aktuellerTag + 7) - 6 : $aktuellerTag - 6) . "', '" .
    tagumwandeln((($aktuellerTag - 5) < 0) ? ($aktuellerTag + 7) - 5 : $aktuellerTag - 5) . "', '" .
    tagumwandeln((($aktuellerTag - 4) < 0) ? ($aktuellerTag + 7) - 4 : $aktuellerTag - 4) . "', '" .
    tagumwandeln((($aktuellerTag - 3) < 0) ? ($aktuellerTag + 7) - 3 : $aktuellerTag - 3) . "', '" .
    tagumwandeln((($aktuellerTag - 2) < 0) ? ($aktuellerTag + 7) - 2 : $aktuellerTag - 2) . "', '" .
    tagumwandeln((($aktuellerTag - 1) < 0) ? ($aktuellerTag + 7) - 1 : $aktuellerTag - 1) . "', '" .
    tagumwandeln($aktuellerTag) . "'";

foreach ($tankstellen as $key => $value) {

    $preise = '';

    for($i = 6; $i >= 0; $i--) {

        $abfragetag = ($tag - ($i + 1)) < 1 ? (($tag - ($i + 1)) + $tageVormonat) : ($tag - ($i + 1));
        $abfragemonat = ($tag - ($i + 1)) < 1 ? ($monat - 1) < 1 ? 12 : $monat - 1 : $monat;
        $abfragejahr = ($monat - 1) < 1 ? $jahr - 1 : $jahr;

        $von = "$jahr-$abfragemonat-" . $abfragetag . " 00:00:00";
        $bis = "$jahr-$abfragemonat-" . $abfragetag . " 23:59:59";

        $sql = "SELECT min($diagramm[benzinart])
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
<div class="diagramm">
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