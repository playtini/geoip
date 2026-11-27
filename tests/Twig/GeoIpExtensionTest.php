<?php

namespace Playtini\GeoIp\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Playtini\GeoIp\GeoIp;
use Playtini\GeoIp\Twig\GeoIpExtension;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Twig\TwigFilter;

class GeoIpExtensionTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<GeoIp> */
    private ObjectProphecy $geoIp;

    private GeoIpExtension $geoIpExtension;

    protected function setUp(): void
    {
        $this->geoIp = $this->prophesize(GeoIp::class);
        $this->geoIpExtension = new GeoIpExtension($this->geoIp->reveal());
    }

    public function testGetFilters(): void
    {
        $filters = $this->geoIpExtension->getFilters();

        $this->assertSame(
            ['domain_ip', 'ip_country_code', 'ip_flag', 'country_code_flag'],
            array_map(static fn (TwigFilter $filter): string => $filter->getName(), $filters),
        );
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

    public function testDomainIp(): void
    {
        // An IP literal resolves to itself without a DNS query
        $this->assertSame('127.0.0.1', $this->geoIpExtension->domainIp('127.0.0.1'));
        $this->assertSame('', $this->geoIpExtension->domainIp(null));
    }

    public function testEmptyValuesGiveNoCountryAndNoFlag(): void
    {
        $this->assertSame('', $this->geoIpExtension->ipCountryCode(null));
        $this->assertSame('', $this->geoIpExtension->countryCodeFlag(''));
        $this->assertSame('', $this->geoIpExtension->countryCodeFlag(null));
    }
}
