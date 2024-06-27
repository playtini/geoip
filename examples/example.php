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

echo $geoIp->country('8.8.8.8') . "\n"; // US

print_r($geoIp->city('190.0.0.1'));
/*
    [country] => CO
    [city] => Medellín
    [subdivision] => Antioquia
    [subdivision1] => Antioquia
    [subdivision2] =>
    [subdivision3] =>
    [subdivision_code] => ANT
    [postal] => 050021
    [accuracy_radius] => 10
    [latitude] => 6.2529
    [longitude] => -75.5646
    [timezone] => America/Bogota
 */

echo $geoIp->maxMindCountry('190.0.0.1') . "\n"; // CO

print_r($geoIp->maxMindCity('190.0.0.1'));
/*
    [country] => CO
    [city] => Medellín
    [subdivision] => Antioquia
    [subdivision1] => Antioquia
    [subdivision2] =>
    [subdivision3] =>
    [subdivision_code] => ANT
    [postal] => 050021
    [accuracy_radius] => 10
    [latitude] => 6.2529
    [longitude] => -75.5646
    [timezone] => America/Bogota
 */

print_r($geoIp->maxMindAsn('190.0.0.1'));
/*
    [org] => EPM Telecomunicaciones S.A. E.S.P.
    [num] => 13489
    [net] => 190.0.0.0/18
*/

echo $geoIp->ip2LocationCountry('190.0.0.1') . "\n"; // CO

echo $geoIp->ip2LocationProxyType('190.0.0.1') . "\n"; // -
echo $geoIp->ip2LocationProxyType('1.2.3.4') . "\n"; // VPN
echo $geoIp->ip2LocationProxyType('8.8.8.8') . "\n"; // DCH
