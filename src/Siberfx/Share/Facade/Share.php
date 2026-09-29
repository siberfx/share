<?php

declare(strict_types=1);

namespace Siberfx\Share\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Siberfx\Share\Share load(?string $url, ?string $title = '', ?string $media = '')
 * @method static array<string, string>|object services(mixed ...$services)
 * @method static bool has(string $service)
 * @method static string generateUrl(string $serviceId, ?string $separator = null)
 * @method static \Illuminate\Support\HtmlString render(array|string|null $services = null, ?string $iconSet = null, ?string $theme = null, bool $labels = false)
 * @method static array<string, array{service: string, label: string, url: string, icon: string, external: bool}> buttons(array|string|null $services = null, ?string $iconSet = null)
 * @method static string icon(string $service, ?string $iconSet = null)
 * @method static \Illuminate\Support\HtmlString styles(?string $iconSet = null)
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
