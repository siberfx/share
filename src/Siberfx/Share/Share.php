<?php

declare(strict_types=1);

namespace Siberfx\Share;

use BadMethodCallException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use InvalidArgumentException;

class Share
{
    /**
     * Variables a service may expose to its URL via the `only` option.
     */
    protected const array SHAREABLE = ['url', 'title', 'media'];

    /**
     * Button themes shipped with the package.
     */
    public const array THEMES = ['bootstrap', 'tailwind', 'plain'];

    protected string $url = '';

    protected string $title = '';

    protected string $media = '';

    public function __construct(protected Container $app)
    {
    }

    /**
     * Set the link, title and media that will be shared.
     */
    public function load(?string $url, ?string $title = '', ?string $media = ''): static
    {
        $this->url = (string) $url;
        $this->title = (string) $title;
        $this->media = (string) $media;

        return $this;
    }

    /**
     * Generate links for several services at once.
     *
     * Accepts service names as variadic arguments or as a single array. Pass
     * `true` as the last argument to receive an object instead of an array.
     * With no services given, links for every configured service are returned.
     *
     * @return array<string, string>|object
     */
    public function services(mixed ...$services): array|object
    {
        $asObject = false;
        if ($services !== [] && end($services) === true) {
            $asObject = true;
            array_pop($services);
        }

        if (isset($services[0]) && is_array($services[0])) {
            $services = $services[0];
        }

        if ($services === []) {
            $services = array_keys($this->config('social-share.services', []));
        }

        $links = [];
        foreach ($services as $service) {
            $links[$service] = $this->generateUrl($service);
        }

        return $asObject ? (object) $links : $links;
    }

    /**
     * Determine whether a service is configured.
     */
    public function has(string $service): bool
    {
        return is_array($this->config("social-share.services.$service"));
    }

    /**
     * Generate the share link for a single service.
     *
     * @throws BadMethodCallException when the service is not configured.
     */
    public function generateUrl(string $serviceId, ?string $separator = null): string
    {
        if (! $this->has($serviceId)) {
            throw new BadMethodCallException(sprintf(
                'Share service [%s] is not defined in the social-share.services config.', $serviceId
            ));
        }

        $service = $this->config("social-share.services.$serviceId");
        $separator ??= (string) $this->config('social-share.separator', '&');

        $only = empty($service['only'])
            ? self::SHAREABLE
            : array_intersect((array) $service['only'], self::SHAREABLE);

        $values = [];
        foreach (self::SHAREABLE as $name) {
            $values[$name] = in_array($name, $only, true) ? $this->$name : '';
        }

        if (! empty($service['view'])) {
            $vars = ['service' => $service, 'sep' => $separator] + array_intersect_key($values, array_flip($only));

            return trim(View::make($service['view'], $vars)->render());
        }

        return $this->buildUrl($service, $values, $separator);
    }

    /**
     * Build a query-string based share link from a service definition.
     *
     * @param  array<string, mixed>  $service
     * @param  array<string, string>  $values
     */
    protected function buildUrl(array $service, array $values, string $separator): string
    {
        $params = [];

        if ($values['url'] !== '') {
            $params[] = ($service['urlName'] ?? 'url').'='.rawurlencode($values['url']);
        }

        if ($values['title'] !== '') {
            $params[] = ($service['titleName'] ?? 'title').'='.rawurlencode($values['title']);
        }

        if (isset($service['mediaName']) && $values['media'] !== '') {
            $params[] = $service['mediaName'].'='.rawurlencode($values['media']);
        }

        if (! empty($service['extra'])) {
            $params[] = http_build_query($service['extra'], '', $separator, PHP_QUERY_RFC3986);
        }

        $uri = (string) ($service['uri'] ?? '');
        $query = implode($separator, $params);

        if ($query === '') {
            return $uri;
        }

        $glue = ! str_contains($uri, '?') ? '?' : (str_ends_with($uri, '?') ? '' : $separator);

        return $uri.$glue.$query;
    }

    /**
     * Render share buttons with icons from the given icon set and theme.
     *
     * @param  array<int, string>|string|null  $services  null renders every configured service
     * @param  string|null  $iconSet  fontawesome, lineawesome, bootstrap-icons or a custom set
     * @param  string|null  $theme  bootstrap, tailwind, plain or a custom view name
     */
    public function render(
        array|string|null $services = null,
        ?string $iconSet = null,
        ?string $theme = null,
        bool $labels = false,
    ): HtmlString {
        $theme ??= (string) $this->config('social-share.theme', 'bootstrap');
        $view = in_array($theme, self::THEMES, true) ? "social-share::buttons.$theme" : $theme;

        return new HtmlString(trim(View::make($view, [
            'buttons' => $this->buttons($services, $iconSet),
            'labels' => $labels,
        ])->render()));
    }

    /**
     * Describe share buttons (link, label and icon) without rendering markup.
     *
     * @param  array<int, string>|string|null  $services
     * @return array<string, array{service: string, label: string, url: string, icon: string, external: bool}>
     */
    public function buttons(array|string|null $services = null, ?string $iconSet = null): array
    {
        $services = $services === null
            ? array_keys($this->config('social-share.services', []))
            : (array) $services;

        $set = $this->iconSet($iconSet);

        $buttons = [];
        foreach ($services as $service) {
            // Blade escapes the href, so links are generated with a raw '&'.
            $url = $this->generateUrl($service, '&');

            $buttons[$service] = [
                'service' => $service,
                'label' => (string) ($this->config("social-share.services.$service.label") ?? ucfirst($service)),
                'url' => $url,
                'icon' => $set['icons'][$service] ?? $set['fallback'] ?? '',
                'external' => ! preg_match('/^(mailto|whatsapp|sms|tel):/i', $url),
            ];
        }

        return $buttons;
    }

    /**
     * Get the icon CSS classes for a service in the given icon set.
     */
    public function icon(string $service, ?string $iconSet = null): string
    {
        $set = $this->iconSet($iconSet);

        return (string) ($set['icons'][$service] ?? $set['fallback'] ?? '');
    }

    /**
     * Get the <link> tag that loads an icon set's stylesheet.
     */
    public function styles(?string $iconSet = null): HtmlString
    {
        $set = $this->iconSet($iconSet);

        if (empty($set['stylesheet'])) {
            return new HtmlString('');
        }

        $attributes = ['rel' => 'stylesheet', 'href' => $set['stylesheet']];

        if (! empty($set['integrity'])) {
            $attributes += ['integrity' => $set['integrity'], 'crossorigin' => 'anonymous', 'referrerpolicy' => 'no-referrer'];
        }

        $html = '';
        foreach ($attributes as $name => $value) {
            $html .= sprintf(' %s="%s"', $name, e($value));
        }

        return new HtmlString("<link$html>");
    }

    /**
     * Resolve an icon set definition from config.
     *
     * @return array{stylesheet?: string, integrity?: string, fallback?: string, icons: array<string, string>}
     *
     * @throws InvalidArgumentException when the icon set is not configured.
     */
    protected function iconSet(?string $name): array
    {
        $name ??= (string) $this->config('social-share.icons.default', 'fontawesome');
        $set = $this->config("social-share.icons.sets.$name");

        if (! is_array($set)) {
            throw new InvalidArgumentException(sprintf(
                'Icon set [%s] is not defined in the social-share.icons.sets config.', $name
            ));
        }

        return $set + ['icons' => []];
    }

    protected function config(string $key, mixed $default = null): mixed
    {
        return $this->app->make('config')->get($key, $default);
    }

    /**
     * Dynamically generate a link for the service named after the method.
     *
     * @param  array<int, mixed>  $arguments
     */
    public function __call(string $name, array $arguments): string
    {
        return $this->generateUrl($name);
    }
}
