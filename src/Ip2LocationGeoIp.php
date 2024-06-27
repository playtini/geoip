<?php

namespace Playtini\GeoIp;

use IP2Location\Database;
use Throwable;

class Ip2LocationGeoIp
{
    public function __construct(
        private readonly string $baseDir = '/usr/share/GeoIP/',
        private readonly string $ip2locationCountryFilename = 'IPV6-COUNTRY.BIN',
        private readonly string $ip2locationProxyTypeFilename = 'IP2PROXY-IP-PROXYTYPE-COUNTRY.BIN',
    )
    {
    }

    public function country(?string $ip): string
    {
        if (!$ip) {
            return '';
        }

        try {
            $db = $this->getCountryDatabase();
        } catch (Throwable) {
            return '';
        }

        $country = (string)$db->lookup($ip, Database::COUNTRY_CODE);

        return ($country !== 'UN' && $country !== '-') ? $country : '';
    }

    public function proxyType(?string $ip): string
    {
        if (!$ip) {
            return '';
        }

        try {
            $db = $this->getProxyTypeDatabase();
        } catch (Throwable) {
            return '';
        }

        $records = $db->lookup($ip, Database::ALL);

        $result = $records['countryCode'] ?? '';

        return ($result !== '-') ? $result : '';
    }



    /** @throws Throwable */
    private function getCountryDatabase(): Database
    {
        static $db = null;

        if ($db === null) {
            $db = new Database(
                file: rtrim($this->baseDir, '/') . '/' . ltrim($this->ip2locationCountryFilename, '/'),
                mode: Database::MEMORY_CACHE,
            );
        }

        return $db;
    }

    /** @throws Throwable */
    private function getProxyTypeDatabase(): Database
    {
        static $db = null;

        if ($db === null) {
            $db = new Database(
                file: rtrim($this->baseDir, '/') . '/' . ltrim($this->ip2locationProxyTypeFilename, '/'),
                mode: Database::MEMORY_CACHE,
            );
        }

        return $db;
    }
}
