<?php

namespace Playtini\GeoIp;

use GeoIp2\Database\Reader;
use Throwable;

class MaxMindGeoIp
{
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

    /** @return array<string,string> */
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

    /** @return array<string,string> */
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
            'org' => $record?->autonomousSystemOrganization ?? '',
            'num' => $record?->autonomousSystemNumber ?? '',
            'net' => $record?->network ?? '',
        ];
    }

    /** @throws Throwable */
    private function getAsnDatabase(): Reader
    {
        static $db = null;

        if ($db === null) {
            $db = new Reader(
                rtrim($this->baseDir, '/') .
                '/' .
                ltrim($this->maxMindAsnFilename, '/')
            );
        }

        return $db;
    }

    /** @throws Throwable */
    private function getCityDatabase(): Reader
    {
        static $db = null;

        if ($db === null) {
            $db = new Reader(
                rtrim($this->baseDir, '/') .
                '/' .
                ltrim($this->maxMindCityFilename, '/')
            );
        }

        return $db;
    }

    /** @throws Throwable */
    private function getCountryDatabase(): Reader
    {
        static $db = null;

        if ($db === null) {
            $db = new Reader(
                rtrim($this->baseDir, '/') .
                '/' .
                ltrim($this->maxMindCountryFilename, '/')
            );
        }

        return $db;
    }
}
