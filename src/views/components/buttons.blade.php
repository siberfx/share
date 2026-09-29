@props([
    'url' => null,
    'title' => '',
    'media' => '',
    'services' => null,
    'icons' => null,
    'theme' => null,
    'labels' => false,
])
{{-- A fresh instance keeps the component from overwriting the facade's loaded link. --}}
{{ (new \Siberfx\Share\Share(app()))
    ->load($url ?? url()->current(), $title, $media)
    ->render($services, $icons, $theme, (bool) $labels) }}
