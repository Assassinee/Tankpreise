<?php

require_once __DIR__ . '/../../config/telegramConfig.php';
require_once __DIR__ . '/../../config.php';
require_once 'Telegram.php';

if (array_key_exists($language, $availableLanguages))
{
    require_once __DIR__ . '/../../lang/'.$language.'.php';
} else {

    require_once __DIR__ . '/../../lang/EN.php';
}

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

        $tg->sendMessage($user_id, $languagetext['modules']['telegram'][0]
            . $price . $languagetext['modules']['telegram'][1]
            . $typ
            . $languagetext['modules']['telegram'][2]);

        $tg->addNotification($user_id, $typ, $price);
    }
}