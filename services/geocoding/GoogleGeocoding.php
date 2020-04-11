<?php

require_once 'iGeocoding.php';

class GoogleGeocoding implements Geocoding
{
    private $apiKey;
    private $addres;
    private $city;
    private $postCode;
    private $lat;
    private $lng;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setAddress($address, $city, $postCode): void
    {
        $this->addres = $address;
        $this->city = $city;
        $this->postCode = $postCode;
    }

    public function calculateCoordinates(): bool
    {
        $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json"
            . "?address=$this->addres,$this->postCode,$this->city"
            . "&key=$this->apiKey");

        $data = json_decode($json, true);

        $this->lat = $data['results'][0]['geometry']['location']['lat'];
        $this->lng = $data['results'][0]['geometry']['location']['lng'];

        return $data != null;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }
}