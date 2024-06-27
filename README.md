GeoIP Client
==================

[![](http://poser.pugx.org/playtini/geoip/version)](https://packagist.org/packages/playtini/geoip)
[![](http://poser.pugx.org/playtini/geoip/require/php)](https://packagist.org/packages/playtini/geoip)
[![](https://img.shields.io/packagist/dt/playtini/geoip)](https://packagist.org/packages/playtini/geoip)
![](https://img.shields.io/github/last-commit/playtini/geoip/main)
![](https://img.shields.io/github/actions/workflow/status/playtini/geoip/test.yaml?branch=main)


## Maintainer

This library is created and supported by [Playtini](https://playtini.ua).

We're hiring marketers (FB, Tiktok, UAC, in-app, Google) and developers (PHP, JS): [playtini.ua/jobs](https://playtini.ua/jobs)


## Install

```bash
composer require playtini/geoip
```

## Usage

Usage:

```php
use Playtini\GeoIp\GeoIp;

require_once(__DIR__ . '/vendor/autoload.php');

$geoIp = new GeoIp();

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

```

If your GeoIP files aren't named by default or aren't in the dir `/usr/share/GeoIP/` then pass arguments to constructors.

```php
80.231.192.1
```
