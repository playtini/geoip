<?php

namespace Playtini\GeoIp\Tests;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Playtini\GeoIp\Ip2LocationGeoIp;

/**
 * Runs against the IP2Location LITE DB1 sample that ip2location/ip2location-php ships in its data/ directory.
 *
 * Ip2LocationGeoIp caches each Database in a method-level static, shared by every instance in the process:
 * the first successful open wins. Tests that need a missing database therefore run in a separate process.
 */
class Ip2LocationGeoIpTest extends TestCase
{
    private const string SAMPLE_DIR = __DIR__ . '/../vendor/ip2location/ip2location-php/data';

    private static function geoIp(string $baseDir = self::SAMPLE_DIR): Ip2LocationGeoIp
    {
        return new Ip2LocationGeoIp(
            baseDir: $baseDir . '/', // trailing slash is trimmed
            ip2locationCountryFilename: 'IP2LOCATION-LITE-DB1.BIN',
            // No proxy-type sample exists: proxyType() reads the record's countryCode field of whatever BIN it gets
            ip2locationProxyTypeFilename: '/IP2LOCATION-LITE-DB1.BIN',
        );
    }

    public function testCountry(): void
    {
        self::assertSame('US', self::geoIp()->country('8.8.8.8'));
    }

    public function testCountryOfPrivateIpIsEmpty(): void
    {
        // IP2Location answers "-" for ranges it has no country for
        self::assertSame('', self::geoIp()->country('10.0.0.1'));
    }

    public function testCountryOfEmptyIpIsEmpty(): void
    {
        self::assertSame('', self::geoIp()->country(null));
        self::assertSame('', self::geoIp()->country(''));
    }

    public function testProxyTypeReadsTheCountryCodeField(): void
    {
        self::assertSame('GB', self::geoIp()->proxyType('2.125.160.216'));
        self::assertSame('', self::geoIp()->proxyType('10.0.0.1'));
        self::assertSame('', self::geoIp()->proxyType(null));
    }

    #[RunInSeparateProcess]
    public function testMissingDatabasesGiveEmptyResults(): void
    {
        $geoIp = self::geoIp('/nonexistent');

        self::assertSame('', $geoIp->country('8.8.8.8'));
        self::assertSame('', $geoIp->proxyType('8.8.8.8'));
    }
}
