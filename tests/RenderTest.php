<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use PHPUnit\Framework\Attributes\DataProvider;
use Siberfx\Share\Facade\Share;

class RenderTest extends TestCase
{
    public static function iconSets(): iterable
    {
        yield 'fontawesome' => ['fontawesome', 'fa-brands fa-x-twitter', 'fa-solid fa-share-nodes'];
        yield 'lineawesome' => ['lineawesome', 'las la-share-alt', 'las la-share-alt'];
        yield 'bootstrap-icons' => ['bootstrap-icons', 'bi bi-twitter-x', 'bi bi-share-fill'];
    }

    #[DataProvider('iconSets')]
    public function testIconForServiceAndFallback(string $set, string $xIcon, string $fallback)
    {
        $this->assertSame($xIcon, Share::icon('x', $set));
        $this->assertSame($fallback, Share::icon('unknown-service', $set));
    }

    public function testFontAwesomeHasABrandIconForEveryService()
    {
        foreach (array_keys(config('social-share.services')) as $service) {
            $this->assertArrayHasKey($service, config('social-share.icons.sets.fontawesome.icons'), $service);
        }
    }

    public function testDefaultIconSetComesFromConfig()
    {
        $this->assertSame('fa-brands fa-facebook-f', Share::icon('facebook'));

        config(['social-share.icons.default' => 'bootstrap-icons']);

        $this->assertSame('bi bi-facebook', Share::icon('facebook'));
    }

    public function testUnknownIconSetThrows()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Icon set [nope] is not defined');

        Share::icon('x', 'nope');
    }

    #[DataProvider('iconSets')]
    public function testStylesPrintsPinnedStylesheetWithIntegrity(string $set, string $xIcon, string $fallback)
    {
        $html = Share::styles($set);

        $this->assertInstanceOf(HtmlString::class, $html);
        $this->assertMatchesRegularExpression(
            '#^<link rel="stylesheet" href="https://cdn\.jsdelivr\.net/npm/[^"]+\.min\.css" integrity="sha384-[A-Za-z0-9+/=]{64}" crossorigin="anonymous" referrerpolicy="no-referrer">$#',
            (string) $html
        );
    }

    public function testStylesForSelfHostedSetIsEmpty()
    {
        config(['social-share.icons.sets.local' => ['fallback' => 'icon-share']]);

        $this->assertSame('', (string) Share::styles('local'));
        $this->assertSame('icon-share', Share::icon('x', 'local'));
    }

    public function testButtons()
    {
        $buttons = Share::load('http://www.example.com', 'Hello & bye')->buttons(['x', 'email'], 'bootstrap-icons');

        $this->assertSame([
            'x' => [
                'service' => 'x',
                'label' => 'X',
                'url' => 'https://x.com/intent/post?url=http%3A%2F%2Fwww.example.com&text=Hello%20%26%20bye',
                'icon' => 'bi bi-twitter-x',
                'external' => true,
            ],
            'email' => [
                'service' => 'email',
                'label' => 'Email',
                'url' => 'mailto:?subject=Hello%20%26%20bye&body=http%3A%2F%2Fwww.example.com',
                'icon' => 'bi bi-envelope',
                'external' => false,
            ],
        ], $buttons);
    }

    public function testButtonsDefaultToAllServicesAndLabelFallsBackToName()
    {
        config(['social-share.services.mastodon' => ['uri' => 'https://mastodon.social/share']]);

        $buttons = Share::load('http://www.example.com')->buttons();

        $this->assertSame(array_keys(config('social-share.services')), array_keys($buttons));
        $this->assertSame('Mastodon', $buttons['mastodon']['label']);
        $this->assertSame('fa-solid fa-share-nodes', $buttons['mastodon']['icon']);
    }

    public static function themes(): iterable
    {
        yield 'bootstrap' => ['bootstrap', 'btn btn-sm btn-outline-secondary'];
        yield 'tailwind' => ['tailwind', 'inline-flex items-center gap-2 rounded-md'];
        yield 'plain' => ['plain', '<ul class="social-share">'];
    }

    #[DataProvider('themes')]
    public function testRenderThemes(string $theme, string $marker)
    {
        $html = (string) Share::load('http://www.example.com', 'Example')->render(['x', 'email'], 'fontawesome', $theme);

        $this->assertStringContainsString($marker, $html);
        $this->assertStringContainsString('href="https://x.com/intent/post?url=http%3A%2F%2Fwww.example.com&amp;text=Example"', $html);
        $this->assertStringContainsString('<i class="fa-brands fa-x-twitter" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="fa-solid fa-envelope" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('aria-label="X"', $html);
        $this->assertStringNotContainsString('<span>X</span>', $html);
        $this->assertSame(1, substr_count($html, 'target="_blank" rel="noopener noreferrer"'), 'mailto links must not open a new tab');
    }

    public function testRenderWithLabels()
    {
        $html = (string) Share::load('http://www.example.com')->render('whatsapp', 'lineawesome', 'plain', labels: true);

        $this->assertStringContainsString('<i class="lab la-whatsapp" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<span>WhatsApp</span>', $html);
        $this->assertStringNotContainsString('aria-label=', $html);
    }

    public function testRenderUsesConfiguredDefaults()
    {
        config(['social-share.theme' => 'tailwind', 'social-share.icons.default' => 'bootstrap-icons']);

        $html = (string) Share::load('http://www.example.com')->render(['facebook']);

        $this->assertStringContainsString('rounded-md', $html);
        $this->assertStringContainsString('bi bi-facebook', $html);
    }

    public function testRenderDoesNotDoubleEscapeWithHtmlSeparator()
    {
        config(['social-share.separator' => '&amp;']);

        $html = (string) Share::load('http://www.example.com', 'Example')->render(['x']);

        $this->assertStringContainsString('url=http%3A%2F%2Fwww.example.com&amp;text=Example"', $html);
        $this->assertStringNotContainsString('&amp;amp;', $html);
    }

    public function testRenderEscapesTitle()
    {
        config(['social-share.services.evil' => ['label' => '<b>Evil</b>', 'uri' => 'https://evil.example.com']]);

        $html = (string) Share::load('http://www.example.com', '"><script>')->render(['evil'], labels: true);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<b>', $html);
        $this->assertStringContainsString('&lt;b&gt;Evil&lt;/b&gt;', $html);
    }

    public function testRenderWithCustomThemeView()
    {
        View::addNamespace('fixtures', __DIR__.'/fixtures');

        $html = (string) Share::load('http://www.example.com')->render(['x', 'facebook'], theme: 'fixtures::custom-buttons');

        $this->assertSame('x:fa-brands fa-x-twitter|facebook:fa-brands fa-facebook-f|', $html);
    }

    public function testButtonsComponent()
    {
        $html = Blade::render(
            '<x-social-share::buttons url="http://www.example.com" title="Example" :services="[\'x\', \'reddit\']" icons="bootstrap-icons" theme="tailwind" labels />'
        );

        $this->assertStringContainsString('href="https://x.com/intent/post?url=http%3A%2F%2Fwww.example.com&amp;text=Example"', $html);
        $this->assertStringContainsString('bi bi-reddit', $html);
        $this->assertStringContainsString('<span>Reddit</span>', $html);
        $this->assertStringContainsString('rounded-md', $html);
    }

    public function testButtonsComponentDoesNotOverwriteFacadeState()
    {
        $share = Share::load('http://first.example.com');

        Blade::render('<x-social-share::buttons url="http://second.example.com" :services="[\'x\']" />');

        $this->assertSame('https://x.com/intent/post?url=http%3A%2F%2Ffirst.example.com', $share->x());
    }

    public function testStylesComponent()
    {
        $this->assertSame((string) Share::styles('lineawesome'),
            trim(Blade::render('<x-social-share::styles icons="lineawesome" />')));
    }
}
