<?php
require_once('config.php');

$mysqli = new mysqli($dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass'], $dbConfig['Database']);

$sql = "SELECT TankstellenID From tankstellen";

$statement = $mysqli->prepare($sql);
$statement->execute();
$result = $statement->get_result();

$tankstellenids = '';

while($row = $result->fetch_object()) {

    $tankstellenids .= $row->TankstellenID . ',';
}

$tankstellenids = substr($tankstellenids, 0, -1);

$json = file_get_contents('https://creativecommons.tankerkoenig.de/json/prices.php'
."?ids=$tankstellenids"
."&apikey=$apiKey[tankerkoenig]");

$data = json_decode($json, true);

foreach ($data['prices'] as $key => $value) {

    $status = $value['status'];
    $e5 = 0;
    $e10 = 0;
    $diesel = 0;

    if($status == 'open') {

        $e5 = $value['e5'];
        $e10 = $value['e10'];
        $diesel = $value['diesel'];
    }

$sql = "insert into preise(TankstellenID, Status, E5, E10, Diesel) values('$key', '$status', $e5, $e10, $diesel)";

$mysqli->query($sql);
}
?>