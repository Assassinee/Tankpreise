<?php

//Datenbank
$dbConfig = Array();
$dbConfig['Typ'] = 'mysql';
$dbConfig['Database'] = '';
$dbConfig['User'] = '';
$dbConfig['Pass'] = '';
$dbConfig['Host'] = '';

//Diagram
$diagramm['FarbstaerkeLinie'] = 1;
$diagramm['Farbstaerkeflaeche'] = 0.4;
$diagramm['benzinart'] = 'E5';
$diagramm['linienfarbe'] = Array('255,99,132', '54,162,235', '255,206,86' ,'75,192,192', '153,102,255', '255,159,64');
$diagramm['Stundenzusammenfassen'] = 12;

$benzinarten = [
    'E5' => 'E5',
    'E10' => 'E10',
    'Diesel' => 'Diesel',
];

//rest
$webseitenpasswort = '';
$webseitenzugriff = 1; //1 = öffentlich; 0 = mit Passwort

//APi's
$apiKey = Array();
$apiKey['Tankerkoenig'] = '';
$apiKey['GoogleGeocoding'] = '';
$apiKey['GoogleMaps'] = '';

$services = [
    'Map' => 'GoogleMaps',
    'Geocoding' => 'GoogleGeocoding',
    'Prices' => 'Tankerkoenig'
]
?>
