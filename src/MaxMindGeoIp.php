<?php

namespace Playtini\GeoIp;

use GeoIp2\Database\Reader;
use Throwable;

class MaxMindGeoIp
{
    // Opened on first use, per instance (not a method-level static: that was shared by every instance,
    // so a second instance with another baseDir silently read the first one's databases)
    private ?Reader $asnDb = null;
    private ?Reader $cityDb = null;
    private ?Reader $countryDb = null;

    public function __construct(
        private readonly string $baseDir = '/usr/share/GeoIP/',
        private readonly string $maxMindAsnFilename = 'GeoLite2-ASN.mmdb',
        private readonly string $maxMindCityFilename = 'GeoLite2-City.mmdb',
        private readonly string $maxMindCountryFilename = 'GeoLite2-Country.mmdb',
    )
    {
    }

    public function country(?string $ip): string
    {
        $record = null;

        if ($ip) {
            try {
                $db = $this->getCountryDatabase();
                $record = $db->country($ip);
            } catch (Throwable) {
            }
        }

        $country = $record?->country->isoCode ?? '';

        return ($country !== 'UN' && $country !== '-') ? $country : '';
    }

    /**
     * Empty strings for an unknown IP; accuracy_radius is an int and latitude/longitude are floats when known.
     *
     * @return array<string, string|int|float>
     */
    public function city(?string $ip): array
    {
        $record = null;

        if ($ip) {
            try {
                $db = $this->getCityDatabase();
                $record = $db->city($ip);
            } catch (Throwable) {
            }
        }

        $country = $record?->country->isoCode ?? '';

        return [
            'country' => ($country !== 'UN' && $country !== '-') ? $country : '',
            'city' => $record?->city->name ?? '',
            'subdivision' => $record?->mostSpecificSubdivision->name ?? '',
            'subdivision1' => $record?->subdivisions[0]->name ?? '',
            'subdivision2' => $record?->subdivisions[1]->name ?? '',
            'subdivision3' => $record?->subdivisions[2]->name ?? '',
            'subdivision_code' => $record?->mostSpecificSubdivision->isoCode ?? '',
            'postal' => $record?->postal->code ?? '',
            'accuracy_radius' => $record?->location->accuracyRadius ?? '',
            'latitude' => $record?->location->latitude ?? '',
            'longitude' => $record?->location->longitude ?? '',
            'timezone' => $record?->location->timeZone ?? '',
        ];
    }

    /** @return array<string, string|int> keys org, num (int when known), net */
    public function asn(?string $ip): array
    {
        $record = null;
        if ($ip) {
            try {
                $db = $this->getAsnDatabase();

                $record = $db->asn($ip);
            } catch (Throwable) {
            }
        }

        return [
            'org' => $record->autonomousSystemOrganization ?? '',
            'num' => $record->autonomousSystemNumber ?? '',
            'net' => $record->network ?? '',
        ];
    }

    /** @throws Throwable */
    private function getAsnDatabase(): Reader
    {
        return $this->asnDb ??= new Reader(rtrim($this->baseDir, '/') . '/' . ltrim($this->maxMindAsnFilename, '/'));
    }

    /** @throws Throwable */
    private function getCityDatabase(): Reader
    {
        return $this->cityDb ??= new Reader(rtrim($this->baseDir, '/') . '/' . ltrim($this->maxMindCityFilename, '/'));
    }

    /** @throws Throwable */
    private function getCountryDatabase(): Reader
    {
        return $this->countryDb ??= new Reader(rtrim($this->baseDir, '/') . '/' . ltrim($this->maxMindCountryFilename, '/'));
    }
}
