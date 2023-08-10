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
        return $request->getServerParams()['REMOTE_ADDR'];
    }
}
