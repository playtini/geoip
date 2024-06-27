<?php

use Playtini\GeoIp\GeoIp;
use Playtini\GeoIp\Ip2LocationGeoIp;
use Playtini\GeoIp\MaxMindGeoIp;

require_once(__DIR__ . '/../vendor/autoload.php');

$baseDir = __DIR__ . '/../data/geoip'; // change to your geoip DBs location
$geoIp = new GeoIp(
    new MaxMindGeoIp($baseDir),
    new Ip2LocationGeoIp($baseDir),
);

echo $geoIp->maxMindCountry('80.231.192.1') . "\n"; // CA
echo $geoIp->ip2LocationCountry('80.231.192.1') . "\n"; // DZ

echo $geoIp->country('80.231.192.1') . "\n"; // DZ - IP2Location has higher priority

echo $geoIp->country('80.231.192.1', ['BR', 'MX']) . "\n"; // DZ
// IP2Location still has higher priority, MaxMind country is not among preferred countries

echo $geoIp->country('80.231.192.1', ['CA', 'AU']) . "\n"; // CA
// MaxMind country is among preferred countries; it is used instead of different IP2Location country
