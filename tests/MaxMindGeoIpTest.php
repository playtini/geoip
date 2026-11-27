<?php

namespace Playtini\GeoIp\Tests;

use PHPUnit\Framework\TestCase;
use Playtini\GeoIp\MaxMindGeoIp;

/**
 * Runs against MaxMind's official test databases (tests/Fixtures/geoip, from github.com/maxmind/MaxMind-DB).
 *
 * Each instance opens its own databases (2.0: they used to be method-level statics, shared by every instance).
 */
class MaxMindGeoIpTest extends TestCase
{
    private const string FIXTURES = __DIR__ . '/Fixtures/geoip/';

    private static function geoIp(string $baseDir = self::FIXTURES): MaxMindGeoIp
    {
        return new MaxMindGeoIp(
            baseDir: $baseDir,
            maxMindAsnFilename: 'GeoLite2-ASN-Test.mmdb',
            maxMindCityFilename: '/GeoLite2-City-Test.mmdb', // leading slash is trimmed
            maxMindCountryFilename: 'GeoLite2-Country-Test.mmdb',
        );
    }

    public function testCountry(): void
    {
        self::assertSame('GB', self::geoIp()->country('2.125.160.216'));
    }

    public function testCountryOfUnknownIpIsEmpty(): void
    {
        self::assertSame('', self::geoIp()->country('1.1.1.1'));
    }

    public function testCountryOfEmptyIpIsEmpty(): void
    {
        self::assertSame('', self::geoIp()->country(null));
        self::assertSame('', self::geoIp()->country(''));
    }

    public function testCity(): void
    {
        self::assertSame([
            'country' => 'GB',
            'city' => 'Boxford',
            'subdivision' => 'West Berkshire',
            'subdivision1' => 'England',
            'subdivision2' => 'West Berkshire',
            'subdivision3' => '',
            'subdivision_code' => 'WBK',
            'postal' => 'OX1',
            'accuracy_radius' => 100,
            'latitude' => 51.75,
            'longitude' => -1.25,
            'timezone' => 'Europe/London',
        ], self::geoIp()->city('2.125.160.216'));
    }

    public function testCityWithoutPostalCode(): void
    {
        $city = self::geoIp()->city('81.2.69.142');

        self::assertSame('London', $city['city']);
        self::assertSame('', $city['postal']);
        self::assertSame('', $city['subdivision2']);
    }

    public function testCityOfUnknownIpIsAllEmpty(): void
    {
        self::assertSame(
            ['country', 'city', 'subdivision', 'subdivision1', 'subdivision2', 'subdivision3', 'subdivision_code', 'postal', 'accuracy_radius', 'latitude', 'longitude', 'timezone'],
            array_keys(self::geoIp()->city('1.1.1.1')),
        );
        self::assertSame([''], array_values(array_unique(self::geoIp()->city('1.1.1.1'))));
        self::assertSame([''], array_values(array_unique(self::geoIp()->city(null))));
    }

    public function testAsn(): void
    {
        self::assertSame(
            ['org' => 'Telstra Pty Ltd', 'num' => 1221, 'net' => '1.128.0.0/11'],
            self::geoIp()->asn('1.128.0.0'),
        );
    }

    public function testAsnOfUnknownOrEmptyIpIsEmpty(): void
    {
        self::assertSame(['org' => '', 'num' => '', 'net' => ''], self::geoIp()->asn('1.1.1.1'));
        self::assertSame(['org' => '', 'num' => '', 'net' => ''], self::geoIp()->asn(null));
    }

    public function testMissingDatabasesGiveEmptyResults(): void
    {
        $geoIp = self::geoIp('/nonexistent');

        self::assertSame('', $geoIp->country('2.125.160.216'));
        self::assertSame('', $geoIp->city('2.125.160.216')['country']);
        self::assertSame(['org' => '', 'num' => '', 'net' => ''], $geoIp->asn('1.128.0.0'));
    }

    public function testInstancesDoNotShareDatabases(): void
    {
        self::assertSame('GB', self::geoIp()->country('2.125.160.216'));
        self::assertSame('', self::geoIp('/nonexistent')->country('2.125.160.216'));
    }
}
