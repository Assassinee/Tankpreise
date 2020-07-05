<?php

require_once __DIR__ . '/../../config/telegramConfig.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../services/Services.php';
require_once 'Telegram.php';
require_once '../../lang/loadLang.php';

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
        }

        $price = floatval(str_replace(',', '.', $text[2]));

        $tg = new Telegram();

        $tg->sendMessage($user_id, $languagetext['modules']['telegram']['confirm1']
            . $price . $languagetext['modules']['telegram']['confirm2']
            . $typ
            . $languagetext['modules']['telegram']['confirm3']);

        $tg->addNotification($user_id, $typ, $price);
    } elseif(strpos($message, "/info") === 0) {

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

        $tankerkoenig = $servicesPrices[$services['Prices']];

        $location = $tankerkoenig->getLocation($price[0]['TankstellenID']);

        $tg = new Telegram();

        $tg->sendMessage($user_id, $languagetext['modules']['telegram']['info1']
            . $typ . $languagetext['modules']['telegram']['info2']
            . $price[0]['Name'] . $languagetext['modules']['telegram']['info3']
            . $price[0][$typ] . $languagetext['modules']['telegram']['info4']);

        $tg->sendVenue($user_id, $location['lat'], $location['lng'], $price[0]['Name'], $location['address']);
    }
}