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

IP2Location database has priority for methods `country`, `city`:
* country - if IP2Location data is not empty - don't look up MaxMind
* city - if IP2Location country differs from MaxMind - use IP2Location country and empty city

If you have guesses which countries may IP be from then you can pass the second argument `array $preferredCountries`.
Then even if IP2Location country differs from MaxMind but MaxMind returns country from preferred then MaxMind country will be used.

```php
echo $geoIp->maxMindCountry('80.231.192.1') . "\n"; // CA
echo $geoIp->ip2LocationCountry('80.231.192.1') . "\n"; // DZ

echo $geoIp->country('80.231.192.1') . "\n"; // DZ - IP2Location has higher priority

echo $geoIp->country('80.231.192.1', ['BR', 'MX']) . "\n"; // DZ
// IP2Location still has higher priority, MaxMind country is not among preferred countries

echo $geoIp->country('80.231.192.1', ['CA', 'AU']) . "\n"; // CA
// MaxMind country is among preferred countries; it is used instead of different IP2Location country
```

See `examples/*.php`.


Symfony
-------

Add to `config/services.yaml`

    imports:
        - { resource: '../vendor/playtini/geoip/config/config.yaml' }

You can copy config to your .yaml-files without import and tune for your needs.

Add env `GEOIP_DIR` with all your GeoIP database files. Default - `%kernel.project_dir%/data/geoip`

Use with autowire

    /**
     * @Route("/test", name="test")
     */
    public function test(GeoIp $geoIp): Response
    {
        dd($geoIpParser->country('1.1.1.1'));
    }

`GeoIpExtension` is optional to add but if you added it you have Twig filters:

* `domain_ip`: convert domain name or IP to IP - `'google.com'|domain_ip`, `'1.1.1.1'|domain_ip`
* `ip_country_code`: convert IP to country code - `'1.1.1.1'|ip_country_code` - US, AU, ...
* `ip_flag`: convert IP to HTML with flag - `'1.1.1.1'|ip_flag`
* `country_code_flag`: convert country code to HTML with flag - `'CA'|country_code_flag`

To use flags copy `public/css/flags.css` and `public/img/flags.png` to your public folder.

Add to `base.html.twig` or other template:

    <link rel="stylesheet" href="{{ asset('css/flags.css') }}">
