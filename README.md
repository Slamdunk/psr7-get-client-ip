# PSR-7 Get Client Ip for both IPv4 and IPv6

[![Latest Stable Version](https://img.shields.io/packagist/v/slam/psr7-get-client-ip.svg)](https://packagist.org/packages/slam/psr7-get-client-ip)
[![Downloads](https://img.shields.io/packagist/dt/slam/psr7-get-client-ip.svg)](https://packagist.org/packages/slam/psr7-get-client-ip)
[![CI](https://github.com/Slamdunk/psr7-get-client-ip/actions/workflows/ci.yaml/badge.svg)](https://github.com/Slamdunk/psr7-get-client-ip/actions/workflows/ci.yaml)
[![Infection MSI](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSlamdunk%2Fpsr7-get-client-ip%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/Slamdunk/psr7-get-client-ip/master)

## Installation

`composer require slam/psr7-get-client-ip`

## Motivation & Usage

Knowing the client's IP is needed to distinguish between bad and good actors, and take appropriate countermeasures.
In IPv4 protocol it's an easy job (or it should be https://adam-p.ca/blog/2022/03/x-forwarded-for/):

* If the actor is good, you take its IPv4 and stick its session to it, so to mitigate session hijacking.
* If the actor is bad, you ban its IPv4.

In IPv6 protocol it's a different story though: routers default implementation allow each client to choose and change
their IP within the subnet, which is at least `/64`. Rate-limiting and banning must take this into consideration, see
https://adam-p.ca/blog/2022/02/ipv6-rate-limiting/

The best approach is still in debate; this library takes the following approach:

* If the actor is good, the full IPv6 ir returned
* If the actor is bad, the `/64` relative subnet is returned

```php
$request = new ServerRequest([
    'REMOTE_ADDR' => '1.2.3.4',
]);

var_dump((new Psr7GetClientIp())->forGoodList($request));       // '1.2.3.4'
var_dump((new Psr7GetClientIp())->forNaughtyList($request));    // '1.2.3.4'

$request = new ServerRequest([
    'REMOTE_ADDR' => '2013:b0a7:5d31:fd03:7257:ae71:6cb9:8e1d',
]);

var_dump((new Psr7GetClientIp())->forGoodList($request));       // '2013:b0a7:5d31:fd03:7257:ae71:6cb9:8e1d'
var_dump((new Psr7GetClientIp())->forNaughtyList($request));    // '2013:b0a7:5d31:fd03::/64'
```
