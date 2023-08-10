<?php

declare(strict_types=1);

namespace Slam\Psr7GetClientIp;

use Psr\Http\Message\ServerRequestInterface;

final class Psr7GetClientIp
{
    /** @return non-empty-string */
    public function forGoodList(ServerRequestInterface $request): string
    {
        return $request->getServerParams()['REMOTE_ADDR'];
    }

    /** @return non-empty-string */
    public function forNaughtyList(ServerRequestInterface $request): string
    {
        $ip      = $this->forGoodList($request);
        $in_addr = \inet_pton($ip);
        \assert(false !== $in_addr);
        if (16 !== \strlen($in_addr)) {
            return $ip;
        }

        $in_addr[8]  = "\0";
        $in_addr[9]  = "\0";
        $in_addr[10] = "\0";
        $in_addr[11] = "\0";
        $in_addr[12] = "\0";
        $in_addr[13] = "\0";
        $in_addr[14] = "\0";
        $in_addr[15] = "\0";

        return \inet_ntop($in_addr) . '/64';
    }
}
