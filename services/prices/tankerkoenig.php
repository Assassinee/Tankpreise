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

    public function setData($lat, $lng, $radius): void
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->radius = $radius;
    }

    public function getStations(): array
    {
        $json = file_get_contents('https://creativecommons.tankerkoenig.de/json/list.php'
            ."?lat=$this->lat"
            ."&lng=$this->lng"
            ."&rad=$this->radius"
            ."&sort=dist"
            ."&type=all"
            ."&apikey=$this->apiKey");

        $data = json_decode($json, true);

        $stationData = null;

        foreach ($data['stations'] as $gasStation)
        {
            $station = null;

            $gasStationAddress = $gasStation['street']
                . (($gasStation['houseNumber'] != '')
                    ? ' ' . $gasStation['houseNumber']
                    : '') . ', ' . $gasStation['place'];

            $station['id'] = $gasStation['id'];
            $station['name'] = $gasStation['name'];
            $station['adresse'] = $gasStationAddress;
            $station['entfernung'] = $gasStation['dist'];
            $station['lat'] = $gasStation['lat'];
            $station['lng'] = $gasStation['lng'];

            $stationData[] = $station;
        }
        return $stationData;
    }

    public function getPrice($stations): array
    {
        $prices = Array();
        $stationids = '';

        foreach ($stations as $key => $value)
        {
            $stationids .= $value . ',';
        }

        $stationids = substr($stationids, 0, -1);

        $json = file_get_contents('https://creativecommons.tankerkoenig.de/json/prices.php'
                ."?ids=$stationids"
                ."&apikey=$this->apiKey");

        $data = json_decode($json, true);

        foreach ($data['prices'] as $key => $value)
        {
            $prices[$key]['status'] = $value['status'];

            if ($value['status'] == 'open')
            {
                $prices[$key]['e5'] = $value['e5'];
                $prices[$key]['e10'] = $value['e10'];
                $prices[$key]['diesel'] = $value['diesel'];
            }
            else
            {
                $prices[$key]['e5'] = 0;
                $prices[$key]['e10'] = 0;
                $prices[$key]['diesel'] = 0;
            }
        }
        return $prices;
    }

    public function getLocation($stationID)
    {
        $json = file_get_contents('https://creativecommons.tankerkoenig.de/json/detail.php'
            ."?id=$stationID"
            ."&apikey=$this->apiKey");

        $data = json_decode($json, true);

        $location = [
            'lat' => $data['station']['lat'],
            'lng' => $data['station']['lng'],
            'address' => $data['station']['street']
        ];

        return $location;
    }
}