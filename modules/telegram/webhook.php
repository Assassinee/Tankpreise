<?php

require_once __DIR__ . '/../../config/telegramConfig.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../services/Services.php';
require_once 'Telegram.php';
require_once __DIR__ . '/../../lang/loadLang.php';

$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

if(in_array($update['message']['chat']['id'], $telegramConfig['users']))
{
    if (strpos($message, "/preisinfo") === 0)
    {
        $text = strtolower($update['message']['text']);

        $text = explode(' ', $text);

        $user_id = $update['message']['chat']['id'];
        $typ = '';
        $price = 0;

        switch ($text[1])
        {
            case 'e5':
                $typ = 'E5';
                break;
            case 'e10':
                $typ = 'E10';
                break;
            case 'diesel':
                $typ = 'Diesel';
                break;
            default:
                $typ = $defaultPetrolType;
                break;
        }

        $price = floatval(str_replace(',', '.', $text[2]));

        $telegram = new Telegram();

        $telegram->sendMessage($user_id, $languagetext['modules']['telegram']['confirm1']
            . $price . $languagetext['modules']['telegram']['confirm2']
            . $typ
            . $languagetext['modules']['telegram']['confirm3']);

        $telegram->addNotification($user_id, $typ, $price);
    }
    elseif(strpos($message, "/info") === 0) {

        $text = strtolower($update['message']['text']);

        $text = explode(' ', $text);

        $user_id = $update['message']['chat']['id'];
        $typ = '';

        switch ($text[1])
        {
            case 'e5':
                $typ = 'E5';
                break;
            case 'e10':
                $typ = 'E10';
                break;
            case 'diesel':
                $typ = 'Diesel';
                break;
            default:
                $typ = $defaultPetrolType;
                break;
        }

        try {

            $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
        } catch (PDOException $e) {

            echo $e->getMessage();
            exit();
        }

        $sql = "SELECT tankstellen.Name, tankstellen.TankstellenID, preise.$typ FROM preise, tankstellen where zeit = (SELECT Zeit FROM `preise` order by Zeit desc LIMIT 1) and Status = 'open' and tankstellen.TankstellenID = preise.TankstellenID order by :typ asc limit 1";

        $command = $db->prepare($sql);

        $command->bindParam(':typ', $typ);

        $command->execute();

        $price = $command->fetchAll();

        $telegram = new Telegram();

        if ($price != null)
        {
            $tankerkoenig = $servicesPrices[$services['Prices']];

            $location = $tankerkoenig->getLocation($price[0]['TankstellenID']);

            $telegram->sendMessage($user_id, $languagetext['modules']['telegram']['info1']
                . $typ . $languagetext['modules']['telegram']['info2']
                . $price[0]['Name'] . $languagetext['modules']['telegram']['info3']
                . $price[0][$typ] . $languagetext['modules']['telegram']['info4']);

            $telegram->sendVenue($user_id, $location['lat'], $location['lng'], $price[0]['Name'], $location['address']);
        }
        else
        {
            $telegram->sendMessage($user_id, $languagetext['modules']['telegram']['info5']);
        }
    }
    elseif ($update["message"]['location'] != null) {

        $lat = $update["message"]['location']['latitude'];
        $lng = $update["message"]['location']['longitude'];
        $BENZINART = 'E5';

        $tankerkoenig = $servicesPrices[$services['Prices']];

        $tankerkoenig->setData($lat, $lng, 5);

        $gasstations = $tankerkoenig->getStations();
        $gasstationsfilter = array();

        foreach ($gasstations as $key => $value)
        {
            if ($value['isOpen'] != null && $value[$BENZINART] != null)
            {
                $gasstationsfilter[] = $value;
            }
        }

        usort($gasstationsfilter, function($item1, $item2) use ($BENZINART) { return $item1[$BENZINART] <=> $item2[$BENZINART]; });

        $telegram = new Telegram();

        if (count($gasstationsfilter) == 0)
        {
            $telegram->sendMessage($chatId, $languagetext['modules']['telegram']['location5']);
        }
        else
        {
            $telegram->sendMessage($chatId, $languagetext['modules']['telegram']['location1']
                . $BENZINART . $languagetext['modules']['telegram']['location2']
                . $gasstationsfilter[0]['name'] . $languagetext['modules']['telegram']['location3']
                . $gasstationsfilter[0][$BENZINART] . $languagetext['modules']['telegram']['location4']);

            $telegram->sendVenue($chatId, $gasstationsfilter[0]['lat'], $gasstationsfilter[0]['lng'], $gasstationsfilter[0]['name'], $gasstationsfilter[0]['adresse']);
        }
    }
}