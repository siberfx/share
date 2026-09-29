<?php

declare(strict_types=1);

namespace Siberfx\Share;

use BadMethodCallException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\View;

class Share
{
    /**
     * Variables a service may expose to its URL via the `only` option.
     */
    protected const array SHAREABLE = ['url', 'title', 'media'];

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
    public function generateUrl(string $serviceId): string
    {
        if (! $this->has($serviceId)) {
            throw new BadMethodCallException(sprintf(
                'Share service [%s] is not defined in the social-share.services config.', $serviceId
            ));
        }

        $service = $this->config("social-share.services.$serviceId");
        $separator = (string) $this->config('social-share.separator', '&');

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
