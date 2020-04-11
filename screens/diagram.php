<?php

try {

    $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
} catch (PDOException $e) {

    echo $e->getMessage();
    exit();
}

$sql = 'Select TankstellenID, Name, Farbe From tankstellen';

$command = $db->query($sql);

$gasStations = Array();

foreach ($command as $key => $value)
{
    $gasStation = Array();

    $gasStation = [
            'TankstellenID' => $value['TankstellenID'],
            'Name' => $value['Name'],
            'Farbe' => $value['Farbe'],
            'Preise' => null
    ];

    $gasStations[] = $gasStation;
}

foreach ($gasStations as &$gasStation)
{
    //TODO:Benzinart
    $sql = 'SELECT Zeit, ' . $BENZINART . ' From preise where TankstellenID = :id and Status = "open" and Zeit >= :zeit';

    $command = $db->prepare($sql);

    $id = $gasStation['TankstellenID'];
    $command->bindParam(':id', $id);

    $lastcomphour = time() - (time() % 3600);
    $startTime = $lastcomphour - (6 * 60 * 60);
    $time = date('o-n-j H:i:s', $startTime);
    $command->bindParam(':zeit', $time);

    $command->execute();

    $price = Array();

    while ($query = $command->fetch(PDO::FETCH_ASSOC))
    {
        //TODO:Benzinart
        $price[strtotime($query['Zeit'])] = $query[$BENZINART];
    }

    $gasStation['Preise'] = $price;
}

unset($gasStation);

$gasStationPrices = Array();

foreach ($gasStations as $gasStation)
{
    foreach ($gasStation['Preise'] as $key => $value)
    {
        $gasStationPrices[$key][$gasStation['TankstellenID']] = $value;
    }
}

ksort($gasStationPrices);

unset($key, $value);

$dataset = "";

foreach ($gasStations as $gasStation)
{
    $priceAsString = '';

    foreach ($gasStationPrices as $key => $value)
    {
        $query = null;

        foreach ($value as $key2 => $value2)
        {
            if ($key2 == $gasStation['TankstellenID'])
            {
                $query = $value2;
            }
        }
        $priceAsString .= $query == null ? null : $query;
        $priceAsString .= ',';
    }

    $priceAsString = substr($priceAsString, 0, -1);

    $dataset .= "{ label: '$gasStation[Name]',
        backgroundColor: 'rgba($gasStation[Farbe],$diagramm[Farbstaerkeflaeche])',
        borderColor: 'rgba($gasStation[Farbe],$diagramm[FarbstaerkeLinie])',
        data: [$priceAsString]
    },";
}

$labels = '';

foreach ($gasStationPrices as $key => $value)
{
    $temp = date('G:i', $key);
    $labels .= "'$temp',";
}
$labels = substr($labels, 0, -1);
?>
<div class="Diagramm">
    <canvas id="myChart"></canvas>
    <script src="../js/Chart.js"></script>
    <script>
        var myChartObject = document.getElementById('myChart');

        var chart = new Chart(myChartObject, {
            type: 'line',
            data: {
                labels: [<?php echo $labels; ?>],
                datasets: [<?php echo $dataset; ?>]
            },
            options: {
                animation: {
                    duration: 2000
                }
            }
        });
    </script>
</div>