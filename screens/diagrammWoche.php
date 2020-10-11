<?php
$lastFullDay = strtotime("today", time());

try
{
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

$startTime = $lastFullDay - 7 * 24 * 60 * 60;
$pricePerTime = array();

unset($key, $value);

foreach ($gasStations as $key => &$value)
{
    //TODO:Benzinart
    $sql = 'SELECT Zeit, ' . $BENZINART . ' From preise where TankstellenID = :id and Status = "open" and Zeit >= :zeit';

    $command = $db->prepare($sql);

    $id = $value['TankstellenID'];
    $command->bindParam(':id', $id);

    $time = date('o-n-j H:i:s', $startTime);
    $command->bindParam(':zeit', $time);

    $command->execute();

    $price = Array();

    while ($query = $command->fetch(PDO::FETCH_ASSOC))
    {
        $price[strtotime($query['Zeit'])] = $query[$BENZINART];
        $pricePerTime[strtotime($query['Zeit'])] = null;
    }
    $value['Preise'] = $price;
}

ksort($pricePerTime);

foreach ($gasStations as $key => &$value)
{
    foreach ($pricePerTime as $key2 => $value2)
    {
        if (!key_exists($key2, $value['Preise']))
        {
            $value['Preise'][$key2] = 0;
        }
    }
    ksort($value['Preise']);
}

$timeJump = 60 * 60 * $diagramm['Stundenzusammenfassen'];

foreach ($gasStations as $key => &$value)
{
    $priceSummarized = Array();
    $timeStart = $startTime;
    $timeEnd = $startTime + $timeJump;
    $price = 0;

    foreach ($value['Preise'] as $key2 => $value2)
    {
        if ($key2 >= $timeEnd)
        {
            $priceSummarized[$timeStart] = $price;
            $price = 0;
            $timeStart = $timeEnd;
            $timeEnd += $timeJump;
        }

        if ($value2 < $price)
        {
            if ($value2 != 0)
            {
                $price = $value2;
            }
        } else {
            if ($price == 0)
            {
                if ($value2 != 0)
                {
                    $price = $value2;
                }
            }
        }
    }

    $value['Preise'] = $priceSummarized;
}

$tankpreise = Array();
unset($key, $value);

foreach ($gasStations as $key => $value)
{
    foreach ($value['Preise'] as $key2 => $value2)
    {
        $tankpreise[$key2][$value['TankstellenID']] = $value2;
    }
}

ksort($tankpreise);

$datenset = "";

foreach ($gasStations as $tanke)
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
    $temp = date('d.m G:i', $preise);

    $day = $languagetext['diagramweek'][date('N', $preise)];

    $labels .= "'$day $temp',";
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