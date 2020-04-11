<?php

require_once 'iMap.php';

class GoogleMaps implements Map
{
    private $apiKey;
    private $lat;
    private $lng;
    private $title;
    private $markers;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setData($lat, $lng, $title): void
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->title = $title;
    }

    public function addMarker($lat, $lng, $title, $icon): void
    {
        $this->markers .= "new google.maps.Marker({position: {lat: $lat, lng: $lng}, map: map, title: '$title', icon: '$icon'});";
    }

    public function getMap(): string
    {
        return '<div style="margin-left: auto; margin-right: auto; height: 50%; width: 70%;"><div style="height: 100%;" id="map"></div></div>';
    }

    public function getJS(): string
    {
        return "<script>
                    function initMap() {
                      
                        var map = new google.maps.Map(document.getElementById('map'), {
                            center: {lat: $this->lat, lng: $this->lng},
                            zoom: 15
                        });
                    
                        new google.maps.Marker({
                            position: {lat: $this->lat, lng: $this->lng},
                            map: map,
                            title: 'Angabe: $this->title'
                        });
                        $this->markers;
                    }
                  </script>
                  <script src=\"https://maps.googleapis.com/maps/api/js?key=$this->apiKey&callback=initMap\" async defer></script>";
    }
}