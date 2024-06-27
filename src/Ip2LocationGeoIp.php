<?php

namespace Playtini\GeoIp;

use IP2Location\Database;
use Throwable;

class Ip2LocationGeoIp
{
    // https://www.ip2location.com/database/px2-ip-proxytype-country

    // Anonymizing VPN services.
    // These services offer users a publicly accessible VPN for the purpose of hiding their IP address.
    // Anonymity: High
    public const PROXY_VPN = 'VPN';

    // Tor Exit Nodes.
    // The Tor Project is an open network used by those who wish to maintain anonymity.
    // Anonymity: High
    public const PROXY_TOR = 'TOR';

    // Hosting Provider, Data Center or Content Delivery Network.
    // Since hosting providers and data centers can serve to provide anonymity, the Anonymous IP database flags IP addresses associated with them.
    // Anonymity: Low
    public const PROXY_DCH = 'DCH';

    // Public Proxies.
    // These are services which make connection requests on a user's behalf. Proxy server software can be configured by the administrator to listen on
    // some specified port. These differ from VPNs in that the proxies usually have limited functions compare to VPNs.
    // Anonymity: High
    public const PROXY_PUB = 'PUB';

    // Web Proxies. These are web services which make web requests on a user's behalf. These differ from VPNs or Public Proxies in that they are simple
    // web-based proxies rather than operating at the IP address and other ports level.
    // Anonymity: High
    public const PROXY_WEB = 'WEB';

    // Search Engine Robots. These are services which perform crawling or scraping to a website, such as, the search engine spider or bots engine.
    // Anonymity: Low
    public const PROXY_SES = 'SES';

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
