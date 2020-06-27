<?php
header("Content-Type: application/json; charset=UTF-8");
require '../config/config.php';

if(!empty($_GET['action']))
{
    $action = $_GET['action'];
    @$stationID = $_GET['stationid'];
    $gastyp = isset($_GET['typ']) ? $_GET['typ'] : $diagramm['benzinart'];
    $found = false;

    //DB
    try {

        $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
    } catch (PDOException $e) {

        echo $e->getMessage();
        exit();
    }

    if ($action == 'price') {

        $sql = "SELECT E5, E10, Diesel FROM preise WHERE TankstellenID = :gasstationid order by Zeit desc Limit 1";

        $command = $db->prepare($sql);

        $command->bindParam(':gasstationid', $stationID);

        $command->execute();

        $price = $command->fetch();

        $found = true;
        response(200, '', array('E5' => $price['E5'], 'E10' => $price['E10'], 'Diesel' => $price['Diesel']));
    }

    if(!$found)
    {
        response(200,"Invalid name",NULL);
    }
}
else
{
    response(400,"Invalid Request",NULL);
}

function response($status, $message, $data)
{
    header('HTTP/1.1 ' . $status);

    $response = Array();
    $response['status'] = $status;
    $response['message'] = $message;
    $response['data'] = $data;

    $json_response = json_encode($response);
    echo $json_response;
}