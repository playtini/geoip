<?php

namespace Playtini\Tests\GeoIp\Twig;

use PHPUnit\Framework\TestCase;
use Playtini\GeoIp\GeoIp;
use Playtini\GeoIp\Twig\GeoIpExtension;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Twig\TwigFilter;

class GeoIpExtensionTest extends TestCase
{
    use ProphecyTrait;

    private GeoIp|ObjectProphecy $geoIp;

    private GeoIpExtension $geoIpExtension;

    protected function setUp(): void
    {
        $this->geoIp = $this->prophesize(GeoIp::class);
        $this->geoIpExtension = new GeoIpExtension($this->geoIp->reveal());
    }

    public function testGetFilters(): void
    {
        $filters = $this->geoIpExtension->getFilters();

        $this->assertIsArray($filters);
        $this->assertInstanceOf(TwigFilter::class, $filters[0]);
    }

    public function testCountryCodeFlag(): void
    {
        $html = $this->geoIpExtension->countryCodeFlag('RU');

        $this->assertSame('<span style="display: inline-block" title="RU" class="flag flag-ru"></span><span class="invisible-report">RU</span>', $html);
    }

    public function testIpCountryCode(): void
    {
        $this->geoIp->country('95.31.18.119')->shouldBeCalledTimes(1)->willReturn('RU');

        $html = $this->geoIpExtension->ipCountryCode('95.31.18.119');

        $this->assertSame('RU', $html);
    }

    public function testIpFlag(): void
    {
        $this->geoIp->country('95.31.18.119')->shouldBeCalledTimes(1)->willReturn('RU');

        $html = $this->geoIpExtension->ipFlag('95.31.18.119');

        $this->assertSame('<span style="display: inline-block" title="RU" class="flag flag-ru"></span><span class="invisible-report">RU</span>', $html);
    }
}
