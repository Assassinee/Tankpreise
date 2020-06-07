<?php

require_once 'iModules.php';
require_once 'config/telegramConfig.php';

class Telegram implements Modules
{
    public function sendMessage($message): bool
    {
        global $telegramConfig;
        $sendSuccess = true;

        foreach ($telegramConfig['users'] as $key => $value)
        {
            $request_params = [
                'chat_id' => $value,
                'text' => $message
            ];

            $request_url = 'https://api.telegram.org/bot' . $telegramConfig['token'] . '/sendMessage?' . http_build_query($request_params);

            $request = file_get_contents($request_url);

            $sendSuccess = $sendSuccess ? json_decode($request, true)['ok'] : false;
        }
        return $sendSuccess;
    }

    public function run(): void
    {


        $this->sendMessage('Hallo');





    }
}