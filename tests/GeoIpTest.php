<?php

namespace Playtini\GeoIp\Tests;

use Playtini\GeoIp\GeoIp;
use PHPUnit\Framework\TestCase;
use Playtini\GeoIp\Ip2LocationGeoIp;
use Playtini\GeoIp\MaxMindGeoIp;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class GeoIpTest extends TestCase
{
    use ProphecyTrait;

    private GeoIp $geoIp;
    private Ip2LocationGeoIp|ObjectProphecy $ip2LocationGeoIp;
    private MaxMindGeoIp|ObjectProphecy $maxMindGeoIp;

    public function setUp(): void
    {
        $this->ip2LocationGeoIp = $this->prophesize(Ip2LocationGeoIp::class);
        $this->maxMindGeoIp = $this->prophesize(MaxMindGeoIp::class);

        $this->geoIp = new GeoIp(
            maxMindGeoIp: $this->maxMindGeoIp->reveal(),
            ip2LocationGeoIp: $this->ip2LocationGeoIp->reveal(),
        );
    }

    public function testGetCountry(): void
    {
        $this->ip2LocationGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('UA');

        self::assertSame('UA', $this->geoIp->country('1.1.1.1'));
    }

    public function testGetCountry_Ip2LocationEmpty(): void
    {
        $this->ip2LocationGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('');
        $this->maxMindGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('UA');

        self::assertSame('UA', $this->geoIp->country('1.1.1.1'));
    }

    public function testGetCountry_Ip2LocationPreferred(): void
    {
        $this->ip2LocationGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('BR');

        self::assertSame('BR', $this->geoIp->country('1.1.1.1', ['CA', 'AU', 'BR']));
    }

    public function testGetCountry_Ip2LocationNotPreferred(): void
    {
        $this->ip2LocationGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('UA');
        $this->maxMindGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('BR');

        self::assertSame('BR', $this->geoIp->country('1.1.1.1', ['CA', 'AU', 'BR']));
    }

    public function testGetCountry_BothNotPreferred(): void
    {
        $this->ip2LocationGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('UA');
        $this->maxMindGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('BR');

        self::assertSame('UA', $this->geoIp->country('1.1.1.1', ['CA', 'AU']));
    }

    public function testGetCity(): void
    {
        $this->maxMindGeoIp->city('1.1.1.1')->shouldBeCalledTimes(1)->willReturn(['country' => 'UA', 'city' => 'Kyiv']);
        $this->ip2LocationGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('UA');

        self::assertSame(['country' => 'UA', 'city' => 'Kyiv'], $this->geoIp->city('1.1.1.1'));
    }

    public function testGetCity_MaxMindNotPreferred(): void
    {
        $this->maxMindGeoIp->city('1.1.1.1')->shouldBeCalledTimes(1)->willReturn(['country' => 'UA', 'city' => 'Kyiv']);
        $this->ip2LocationGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('BR');

        self::assertSame(['country' => 'BR', 'city' => ''], $this->geoIp->city('1.1.1.1', ['BR', 'CA']));
    }

    public function testGetMaxMindCountry(): void
    {
        $this->maxMindGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('UA');

        self::assertSame('UA', $this->geoIp->maxMindCountry('1.1.1.1'));
    }

    public function testGetMaxMindCity(): void
    {
        $this->maxMindGeoIp->city('1.1.1.1')->shouldBeCalledTimes(1)->willReturn(['country' => 'UA', 'city' => 'Kyiv']);

        self::assertSame(['country' => 'UA', 'city' => 'Kyiv'], $this->geoIp->maxMindCity('1.1.1.1'));
    }

    public function testGetMaxMindAsn(): void
    {
        $this->maxMindGeoIp->asn('1.1.1.1')->shouldBeCalledTimes(1)->willReturn(['org' => 'test']);

        self::assertSame(['org' => 'test'], $this->geoIp->maxMindAsn('1.1.1.1'));
    }

    public function testGetIp2LocationCountry(): void
    {
        $this->ip2LocationGeoIp->country('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('UA');

        self::assertSame('UA', $this->geoIp->ip2LocationCountry('1.1.1.1'));
    }

    public function testGetIp2LocationProxyType(): void
    {
        $this->ip2LocationGeoIp->proxyType('1.1.1.1')->shouldBeCalledTimes(1)->willReturn('VPN');

        self::assertSame('VPN', $this->geoIp->ip2LocationProxyType('1.1.1.1'));
    }
}
