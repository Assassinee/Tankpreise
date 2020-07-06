<?php

require_once __DIR__ . '/../config/config.php';
require_once 'map/GoogleMaps.php';
require_once 'geocoding/GoogleGeocoding.php';
require_once 'prices/tankerkoenig.php';

$servicesMap = [
    'GoogleMaps' => new GoogleMaps($apiKey['GoogleMaps'])
];

$servicesGeocoding = [
    'GoogleGeocoding' => new GoogleGeocoding($apiKey['GoogleGeocoding'])
];

$servicesPrices = [
    'Tankerkoenig' => new Tankerkoenig($apiKey['Tankerkoenig'])
];