<?php

declare(strict_types=1);

namespace Slam\Psr7GetClientIp\Tests;

use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Slam\Psr7GetClientIp\Psr7GetClientIp;

#[CoversClass(Psr7GetClientIp::class)]
final class Psr7GetClientIpTest extends TestCase
{
    /** @param non-empty-string $expected */
    #[DataProvider('provideIpv4Cases')]
    public function testIpv4GoodList(string $expected): void
    {
        $request = new ServerRequest([
            'REMOTE_ADDR' => $expected,
        ]);

        self::assertSame($expected, (new Psr7GetClientIp())->forGoodList($request));
    }

    /** @param non-empty-string $expected */
    #[DataProvider('provideIpv4Cases')]
    public function testIpv4NaughtyList(string $expected): void
    {
        $request = new ServerRequest([
            'REMOTE_ADDR' => $expected,
        ]);

        self::assertSame($expected, (new Psr7GetClientIp())->forNaughtyList($request));
    }

    /** @return list<list<non-empty-string>> */
    public static function provideIpv4Cases(): array
    {
        return [
            ['0.0.0.0'],
            ['1.2.3.4'],
            ['1.102.103.104'],
            ['255.255.255.255'],
        ];
    }

    /** @param non-empty-string $expected */
    #[DataProvider('provideIpv6Cases')]
    public function testIpv6GoodList(string $expected): void
    {
        $request = new ServerRequest([
            'REMOTE_ADDR' => $expected,
        ]);

        self::assertSame($expected, (new Psr7GetClientIp())->forGoodList($request));
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $expected
     */
    #[DataProvider('provideIpv6Cases')]
    public function testIpv6NaughtyList(string $source, string $expected): void
    {
        $request = new ServerRequest([
            'REMOTE_ADDR' => $source,
        ]);

        self::assertSame($expected, (new Psr7GetClientIp())->forNaughtyList($request));
    }

    /** @return list<list<non-empty-string>> */
    public static function provideIpv6Cases(): array
    {
        return [
            ['2013:b0a7:5d31:fd03:7257:ae71:6cb9:8e1d', '2013:b0a7:5d31:fd03::/64'],
            ['::', '::/64'],
            ['2001:db8::', '2001:db8::/64'],
            ['::1234:5678', '::/64'],
            ['2001:db8::1234:5678', '2001:db8::/64'],
            ['2001:0db8:0001:0000:0000:0ab9:c0a8:0102', '2001:db8:1::/64'],
        ];
    }
}
