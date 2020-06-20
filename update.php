<?php

require_once 'config.php';
require_once 'services/Services.php';
require_once 'modules/ModuleManager.php';

$aktuelleZeit = time();

try {

    $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
} catch (PDOException $e) {

    echo $e->getMessage();
    exit();
}

$sql = 'SELECT TankstellenID From tankstellen';

$ergebnis = $db->query($sql);

$tankstellenID = Array();

foreach ($ergebnis as $zeile) {

    $tankstellenID[] = $zeile['TankstellenID'];
}

$api = $servicesPrices[$services['Prices']];

$preise = $api->getPrice($tankstellenID);

foreach ($preise as $key => $value)
{
    $sql = 'insert into preise(TankstellenID, Zeit, Status, E5, E10, Diesel) values (:ID, :zeit, :status, :e5, :e10, :diesel)';

    $kommando = $db->prepare($sql);

    $id = $key;
    $kommando->bindParam(':ID', $key);

    $zeit = date('Y-m-d G:i:s', $aktuelleZeit);
    $kommando->bindParam(':zeit', $zeit);

    $status = $value['status'];
    $kommando->bindParam(':status', $status);

    $e5 = $value['e5'];
    $kommando->bindParam(':e5', $e5);

    $e10 = $value['e10'];
    $kommando->bindParam(':e10', $e10);

    $diesel = $value['diesel'];
    $kommando->bindParam(':diesel', $diesel);

    $kommando->execute();
}

if (array_key_exists($language, Array('DE' => null, 'EN' => null)))
{
    require_once 'lang/'.$language.'.php';
} else {

    require_once 'lang/EN.php';
}

$manager = new ModuleManager();

$manager->runModules();