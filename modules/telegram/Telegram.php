<?php

require_once __DIR__ . '/../iModules.php';
require_once __DIR__ . '/../../config/telegramConfig.php';
require_once __DIR__ . '/../../config/config.php';

class Telegram implements Modules
{
    public function __construct()
    {
        global $telegramConfig;

        if (!file_exists($telegramConfig['xmlfile']))
        {
            $dom = new DOMDocument('1.0', 'utf-8');
            $dom->appendChild($dom->createElement('Telegram'));
            $dom->save($telegramConfig['xmlfile']);
        }
    }

    public function sendMessage($user_id, $message): bool
    {
        global $telegramConfig;

        $request_params = [
            'chat_id' => $user_id,
            'text' => $message,
            'parse_mode' => 'html'
        ];

        $request_url = 'https://api.telegram.org/bot' . $telegramConfig['token'] . '/sendMessage?' . http_build_query($request_params);

        $request = file_get_contents($request_url);

        return json_decode($request, true)['ok'];
    }

    public function sendVenue($user_id, $lat, $lng, $title, $address): bool
    {
        global $telegramConfig;

        $request_params = [
            'chat_id' => $user_id,
            'latitude' => $lat,
            'longitude' => $lng,
            'title' => $title,
            'address' => $address
        ];

        $request_url = 'https://api.telegram.org/bot' . $telegramConfig['token'] . '/sendVenue?' . http_build_query($request_params);

        $request = file_get_contents($request_url);

        return json_decode($request, true)['ok'];
    }

    public function addNotification($user_id, $typ, $price) {

        global $telegramConfig;
        $dom = new DOMDocument();
        $dom->load($telegramConfig['xmlfile']);

        if ($dom->getElementsByTagName('Notifications')->length == 0)
        {
            $dom->documentElement->appendChild($dom->createElement('Notifications'));
        }

        $node = $dom->getElementsByTagName('Notification_' . $user_id);

        if ($node->length >= 1)
        {
            $node->item(0)->parentNode->removeChild($node->item(0));
        }

        $newElement = $dom->createElement('Notification_' . $user_id);
        $newElement->appendChild($dom->createElement('user_id', $user_id));
        $newElement->appendChild($dom->createElement('typ', $typ));
        $newElement->appendChild($dom->createElement('price', $price));

        $dom->getElementsByTagName('Notifications')->item(0)->appendChild($newElement);

        $dom->save($telegramConfig['xmlfile']);
    }

    private function checkprice()
    {
        require_once __DIR__ . '/../../services/Services.php';

        global $telegramConfig;
        global $dbConfig;
        global $languagetext;
        global $servicesPrices;
        global $services;

        $dom = new DOMDocument();
        $dom->load($telegramConfig['xmlfile']);

        if ($dom->getElementsByTagName('Notifications')->length == 1)
        {
            foreach ($telegramConfig['users'] as $key => $value)
            {
                if ($dom->getElementsByTagName('Notification_' . $value)->length == 1)
                {
                    $node = $dom->getElementsByTagName('Notification_' . $value);
                    $nodes = $node->item(0)->childNodes;

                    try
                    {
                        $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
                    } catch (PDOException $e) {

                        echo $e->getMessage();
                        exit();
                    }

                    $typ = $nodes->item(1)->nodeValue;

                    $sql = "SELECT tankstellen.Name, preise.TankstellenID, $typ as 'price' FROM preise, tankstellen WHERE zeit = (SELECT Zeit FROM `preise` order by Zeit desc limit 1) and Status = 'open' and tankstellen.TankstellenID = preise.TankstellenID ORDER by :typ asc limit 1";

                    $command = $db->prepare($sql);

                    $command->bindParam(':typ', $typ);

                    $command->execute();

                    $request = $command->fetchAll();

                    if (floatval($request[0]['price']) != 0 && floatval($request[0]['price']) <= floatval($nodes->item(2)->nodeValue))
                    {
                        $tankerkoenig = $servicesPrices[$services['Prices']];

                        $location = $tankerkoenig->getLocation($request[0]['TankstellenID']);

                        $this->sendMessage($value, $languagetext['modules']['telegram']['check1']
                                                    . $nodes->item(1)->nodeValue
                                                    . $languagetext['modules']['telegram']['check2']
                                                    . $request[0]['Name']
                                                    . $languagetext['modules']['telegram']['check3']
                                                    . $request[0]['price']
                                                    . $languagetext['modules']['telegram']['check4']);

                        $this->sendVenue($value, $location['lat'], $location['lng'], $request[0]['Name'], $location['address']);

                        $node->item(0)->parentNode->removeChild($node->item(0));
                        $dom->save($telegramConfig['xmlfile']);
                    }
                }
            }
        }
    }

    private function getDailyLastUpdate() {

        global $telegramConfig;
        $lastUpdate = 0;
        $dom = new DOMDocument();
        $dom->load($telegramConfig['xmlfile']);

        if ($dom->getElementsByTagName('DailyUpdate')->length == 0)
        {
            $dom->documentElement->appendChild($dom->createElement('DailyUpdate'));

            $dom->save($telegramConfig['xmlfile']);
        } else if ($dom->getElementsByTagName('DailyUpdate')->length == 1) {

            $node = $dom->getElementsByTagName('DailyUpdate');
            $nodes = $node->item(0)->childNodes;

            if ($nodes->length >= 1) {

                $lastUpdate = $nodes->item(0)->nodeValue;
            }
        }
        return $lastUpdate;
    }

    private function setDailyLastUpdate($time) {

        global $telegramConfig;
        $dom = new DOMDocument();
        $dom->load($telegramConfig['xmlfile']);

        $node = $dom->getElementsByTagName('DailyUpdate');

        if ($node->item(0)->childNodes->length >= 1) {

            $node->item(0)->removeChild($node->item(0)->childNodes->item(0));
        }

        $node->item(0)->appendChild($dom->createElement('lastupdate', $time));

        $dom->save($telegramConfig['xmlfile']);
    }

    private function dailyinfo() {

        global $dbConfig;
        global $languagetext;
        global $telegramConfig;
        $time = time();
        $timeday = $time - strtotime("today");
        $lastUpdate = $this->getDailyLastUpdate();

        if ($lastUpdate < strtotime("today")) {

            if ($timeday >= $telegramConfig['dailyinfo']['time']) {

                $typ = $telegramConfig['dailyinfo']['type'];

                try
                {
                    $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
                } catch (PDOException $e) {

                    echo $e->getMessage();
                    exit();
                }

                $sql = "SELECT min(E5) as '$typ' FROM `preise` where Zeit BETWEEN :time1 and :time2 and Status = 'open'";

                $command = $db->prepare($sql);

                $time1 = date('Y-m-d 00:00:00', strtotime("yesterday"));
                $command->bindParam(':time1', $time1);

                $time2 = date('Y-m-d 23:59:59', strtotime("yesterday"));
                $command->bindParam(':time2', $time2);

                $command->execute();

                $price = $command->fetchAll()[0]['E5'];

                foreach ($telegramConfig['dailyinfo']['users'] as $userID) {

                    $this->sendMessage($userID, $languagetext['modules']['telegram']['daily1'] . $typ
                        . $languagetext['modules']['telegram']['daily2'] . $price
                        . $languagetext['modules']['telegram']['daily3']);
                }
                $this->setDailyLastUpdate($time);
            }
        }
    }

    public function run(): void
    {
        $this->checkprice();
        $this->dailyinfo();
    }
}
