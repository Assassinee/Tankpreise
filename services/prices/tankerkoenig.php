<?php

require_once 'iPrices.php';

class Tankerkoenig implements Prices
{
    private $apiKey;
    private $lat;
    private $lng;
    private $radius;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setData($lat, $lng, $radius)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->radius = $radius;
    }

    public function getPrices()
    {
        $json = file_get_contents('https://creativecommons.tankerkoenig.de/json/list.php'
            ."?lat=$this->lat"
            ."&lng=$this->lng"
            ."&rad=$this->radius"
            ."&sort=dist"
            ."&type=all"
            ."&apikey=$this->apiKey");

        $data = json_decode($json, true);

        $formattedData = null;

        foreach ($data['stations'] as $gasStation)
        {
            $tank = null;

            $gasStationAddress = $gasStation['street']
                . (($gasStation['houseNumber'] != '')
                    ? ' ' . $gasStation['houseNumber']
                    : '') . ', ' . $gasStation['place'];

            $tank['id'] = $gasStation['id'];
            $tank['name'] = $gasStation['name'];
            $tank['adresse'] = $gasStationAddress;
            $tank['entfernung'] = $gasStation['dist'];
            $tank['lat'] = $gasStation['lat'];
            $tank['lng'] = $gasStation['lng'];

            $formattedData[] = $tank;
        }
        return $formattedData;
    }
}