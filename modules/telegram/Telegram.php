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

    private function getLastUpdate(): int
    {
        global $telegramConfig;
        $lastupdate = 0;

        $dom = new DOMDocument();
        $dom->load($telegramConfig['xmlfile']);

        $element = $dom->getElementsByTagName('lastupdate');

        if ($element->length == 1)
        {
            $node = $element->item(0);

            $lastupdate = $node->textContent;

            $node->parentNode->removeChild($node);
        }

        $newnode = $dom->createElement('lastupdate', time());
        $dom->getElementsByTagName('Telegram')->item(0)->appendChild($newnode);

        $dom->save($telegramConfig['xmlfile']);

        return $lastupdate;
    }

    public function addNotification($user_id, $typ, $price) {

        global $telegramConfig;
        $dom = new DOMDocument();
        $dom->load(__DIR__ . '/../../' . $telegramConfig['xmlfile']);

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

        $dom->save(__DIR__ . '/../../' . $telegramConfig['xmlfile']);
    }

    private function getMessages() {

        global $telegramConfig;
        global $languagetext;

        $lastupdate = $this->getLastUpdate();

        $request_url = 'https://api.telegram.org/bot' . $telegramConfig['token'] . '/getupdates';

        $request = file_get_contents($request_url);

        $result = json_decode($request, true);

        if ($result['ok'])
        {
            $messages = Array();

            foreach ($result['result'] as $key => $value)
            {
                if (isset($value['message']))
                {
                    if(in_array($value['message']['from']['id'], $telegramConfig['users']))
                    {
                        if($value['message']['date'] >= $lastupdate)
                        {
                            $text = strtolower($value['message']['text']);

                            if (preg_match('/\/preisinfo/', $text))
                            {
                                $text = explode(' ', $text);

                                $user_id = $value['message']['chat']['id'];
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

                                $messages[$user_id]['information']['typ'] = $typ;
                                $messages[$user_id]['information']['price'] = $price;
                            }
                        }
                    }
                }
            }

            foreach ($messages as $key => $value)
            {
                $this->addNotification($key, $value['information']['typ'], $value['information']['price']);
                $this->sendMessage($key, $languagetext['modules']['telegram']['confirm1']
                                                . $price . $languagetext['modules']['telegram']['confirm2']
                                                . $value['information']['typ']
                                                . $languagetext['modules']['telegram']['confirm3']);
            }
        }
    }

    private function checkprice()
    {
        global $telegramConfig;
        global $dbConfig;
        global $languagetext;

        $dom = new DOMDocument();
        $dom->load($telegramConfig['xmlfile']);

        if ($dom->getElementsByTagName('Notifications')->length == 1)
        {
            $petrolprice = NULL;

            foreach ($telegramConfig['users'] as $key => $value)
            {
                if ($dom->getElementsByTagName('Notification_' . $value)->length == 1)
                {
                    if ($petrolprice == NULL)
                    {
                        try
                        {
                            $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
                        } catch (PDOException $e) {

                            echo $e->getMessage();
                            exit();
                        }

                        $sql = 'SELECT min(E5) as "E5", min(E10) as "E10", min(Diesel) as "Diesel" FROM preise WHERE zeit = (SELECT zeit FROM preise ORDER by zeit desc limit 1) and Status = "open"';

                        $command = $db->query($sql);

                        $price = $command->fetchAll();

                        $petrolprice['E5'] = $price[0]['E5'];
                        $petrolprice['E10'] = $price[0]['E10'];
                        $petrolprice['Diesel'] = $price[0]['Diesel'];
                    }

                    $node = $dom->getElementsByTagName('Notification_' . $value);
                    $nodes = $node->item(0)->childNodes;

                    if ($petrolprice[$nodes->item(1)->nodeValue] != 0 && $petrolprice[$nodes->item(1)->nodeValue] <= floatval($nodes->item(2)->nodeValue))
                    {
                        $this->sendMessage($value, $languagetext['modules']['telegram']['check1']
                                                    . $nodes->item(1)->nodeValue
                                                    . $languagetext['modules']['telegram']['check2']
                                                    . $petrolprice[$nodes->item(1)->nodeValue]
                                                    . $languagetext['modules']['telegram']['check3']);

                        $node->item(0)->parentNode->removeChild($node->item(0));
                        $dom->save($telegramConfig['xmlfile']);
                    }
                }
            }
        }
    }

    public function run(): void
    {
        $this->checkprice();
    }
}