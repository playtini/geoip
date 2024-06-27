<?php

namespace Playtini\GeoIp;

class GeoIp
{
    public function __construct(
        private readonly MaxMindGeoIp $maxMindGeoIp = new MaxMindGeoIp(),
        private readonly Ip2LocationGeoIp $ip2LocationGeoIp = new Ip2LocationGeoIp(),
    )
    {
    }

    public function country(?string $ip, array $preferredCountries = []): string
    {
        $country = $this->ip2LocationCountry($ip);

        if (
            $country &&
            (
                !$preferredCountries ||
                in_array($country, $preferredCountries, true)
            )
        ) {
            return $country;
        }

        $country2 = $this->maxMindCountry($ip);
        if (
            $country2 &&
            $preferredCountries &&
            in_array($country2, $preferredCountries, true)
        ) {
            return $country2;
        }

        return ($country !== '') ? $country : $country2;
    }

    public function city(?string $ip, array $preferredCountries = []): array
    {
        $a = $this->maxMindCity($ip);
        $country = $a['country'] ?? '';

        if (
            !$country ||
            !$preferredCountries ||
            !in_array($country, $preferredCountries, true)
        ) {
            $country2 = $this->ip2LocationCountry($ip);
            if ($country2 && $country2 !== $country) {
                foreach ($a as &$v) {
                    $v = '';
                }
                unset($v);
                $a['country'] = $country2;
            }
        }

        return $a;
    }

    public function maxMindCountry(?string $ip): string
    {
        return $this->maxMindGeoIp->country($ip);
    }

    /** @return array<string,string> */
    public function maxMindCity(?string $ip): array
    {
        return $this->maxMindGeoIp->city($ip);
    }

    /** @return array<string,string> */
    public function maxMindAsn(?string $ip): array
    {
        return $this->maxMindGeoIp->asn($ip);
    }

    public function ip2LocationCountry(?string $ip): string
    {
        return $this->ip2LocationGeoIp->country($ip);
    }

    public function ip2LocationProxyType(?string $ip): string
    {
        return $this->ip2LocationGeoIp->proxyType($ip);
    }
}
