<?php

declare(strict_types=1);

namespace Siberfx\Share\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Siberfx\Share\Share load(?string $url, ?string $title = '', ?string $media = '')
 * @method static array<string, string>|object services(mixed ...$services)
 * @method static bool has(string $service)
 * @method static string generateUrl(string $serviceId)
 *
 * @see \Siberfx\Share\Share
 */
class Share extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'share';
    }
}
