<?php
header("Content-Type:application/json");
require '../config.php';

if(!empty($_GET['name']))
{
    $name = $_GET['name'];
    $price = NULL;
    $BENZINART = $_GET['typ'];

    //DB
    try {

        $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
    } catch (PDOException $e) {

        echo $e->getMessage();
        exit();
    }

    if ($name == 'price') {

        $sql = "Select min($BENZINART) From (SELECT $BENZINART FROM preise where Status = 'open' order by Zeit desc limit 5) as letztepreise";

        $kommando = $db->query($sql);

        $price = $kommando->fetch()[0];
    }

    if ($name == 'priceweek') {

        $sql = "SELECT min($BENZINART) FROM preise where Zeit >= :zeit and Status = 'open'";

        $kommando = $db->prepare($sql);

        $letzterTag = strtotime("today", time());
        $zeit = date('o-n-j H:i:s', $letzterTag - 60 * 60 * 24 * 7);
        $kommando->bindParam(':zeit', $zeit);

        $kommando->execute();

        $price = $kommando->fetch()[0];
    }

    if(empty($price))
    {
        response(200,"Product Not Found",NULL);
    }
    else
    {
        response(200,"Product Found",$price);
    }
}
else
{
    response(400,"Invalid Request",NULL);
}

function response($status,$status_message,$data)
{
    header("HTTP/1.1 ".$status);

    $response['status'] = $status;
    $response['status_message'] = $status_message;
    $response['data'] = $data;

    $json_response = json_encode($response);
    echo $json_response;
}