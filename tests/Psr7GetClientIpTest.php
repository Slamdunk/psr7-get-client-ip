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
    public function testIpv4(string $expected): void
    {
        $request = new ServerRequest([
            'REMOTE_ADDR' => $expected,
        ]);

        self::assertSame($expected, (new Psr7GetClientIp())->forGoodList($request));
        self::assertSame($expected, (new Psr7GetClientIp())->forNaughtyList($request));
    }

    /** @return list<list<non-empty-string>> */
    public static function provideIpv4Cases(): array
    {
        return [
            ['0.0.0.0'],
            ['1.2.3.4'],
            ['01.102.103.104'],
            ['255.255.255.255'],
        ];
    }
}
