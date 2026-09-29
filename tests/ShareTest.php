<?php

use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\DataProvider;
use Siberfx\Share\Facade\Share;
use Siberfx\Share\Share as ShareManager;

class ShareTest extends TestCase
{
    protected const array EXPECTED = [
        'bluesky' => 'https://bsky.app/intent/compose?text=Example%20http%3A%2F%2Fwww.example.com',
        'delicious' => 'https://delicious.com/post?url=http%3A%2F%2Fwww.example.com&title=Example',
        'digg' => 'https://digg.com/submit?url=http%3A%2F%2Fwww.example.com&title=Example',
        'email' => 'mailto:?subject=Example&body=http%3A%2F%2Fwww.example.com',
        'evernote' => 'https://evernote.com/clip.action?url=http%3A%2F%2Fwww.example.com&title=Example',
        'facebook' => 'https://facebook.com/sharer/sharer.php?u=http%3A%2F%2Fwww.example.com&title=Example',
        'gmail' => 'https://mail.google.com/mail/?su=http%3A%2F%2Fwww.example.com&body=Example&view=cm&fs=1&to=&ui=2&tf=1',
        'linkedin' => 'https://linkedin.com/shareArticle?url=http%3A%2F%2Fwww.example.com&title=Example&mini=true',
        'pinterest' => 'https://pinterest.com/pin/create/button/?url=http%3A%2F%2Fwww.example.com&description=Example&media=Media',
        'reddit' => 'https://reddit.com/submit?url=http%3A%2F%2Fwww.example.com&title=Example',
        'telegramMe' => 'https://telegram.me/share/url?url=http%3A%2F%2Fwww.example.com&text=Example',
        'threads' => 'https://www.threads.net/intent/post?url=http%3A%2F%2Fwww.example.com&text=Example',
        'tumblr' => 'https://tumblr.com/share?u=http%3A%2F%2Fwww.example.com&t=Example&v=3',
        'twitter' => 'https://twitter.com/intent/tweet?url=http%3A%2F%2Fwww.example.com&text=Example',
        'vk' => 'https://vk.com/share.php?url=http%3A%2F%2Fwww.example.com&title=Example&image=Media&noparse=false',
        'whatsapp' => 'whatsapp://send?text=Example%20http%3A%2F%2Fwww.example.com',
        'x' => 'https://x.com/intent/post?url=http%3A%2F%2Fwww.example.com&text=Example',
    ];

    protected const array CUSTOM = [
        'service' => 'http://service.example.com?url=http%3A%2F%2Fwww.example.com&title=Example&media=Media',
        'service2' => 'http://service2.example.com?url=http%3A%2F%2Fwww.example.com&title=Example&extra1=Extra%201&extra2=Extra%202',
    ];

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('social-share.services.service', [
            'uri' => 'http://service.example.com',
            'mediaName' => 'media',
        ]);

        $app['config']->set('social-share.services.service2', [
            'uri' => 'http://service2.example.com',
            'extra' => ['extra1' => 'Extra 1', 'extra2' => 'Extra 2'],
        ]);
    }

    public static function builtInServices(): iterable
    {
        foreach (self::EXPECTED as $service => $url) {
            yield $service => [$service, $url];
        }
    }

    #[DataProvider('builtInServices')]
    public function testBuiltInService(string $service, string $expected)
    {
        $this->assertSame($expected, Share::load('http://www.example.com', 'Example', 'Media')->$service());
    }

    public function testRenderUrlOnly()
    {
        $this->assertSame('http://service.example.com?url=http%3A%2F%2Fwww.example.com',
            Share::load('http://www.example.com')->service());
    }

    public function testRenderUrlAndTitle()
    {
        $this->assertSame('http://service.example.com?url=http%3A%2F%2Fwww.example.com&title=Example',
            Share::load('http://www.example.com', 'Example')->service());
    }

    public function testRenderUrlTitleAndMedia()
    {
        $this->assertSame('http://service.example.com?url=http%3A%2F%2Fwww.example.com&title=Example&media=Media',
            Share::load('http://www.example.com', 'Example', 'Media')->service());
    }

    public function testRenderExtra()
    {
        $this->assertSame('http://service2.example.com?url=http%3A%2F%2Fwww.example.com&extra1=Extra%201&extra2=Extra%202',
            Share::load('http://www.example.com')->service2());
    }

    public function testTitleZeroIsNotDropped()
    {
        $this->assertSame('http://service.example.com?url=http%3A%2F%2Fwww.example.com&title=0',
            Share::load('http://www.example.com', '0')->service());
    }

    public function testNullTitleAndMediaAreAccepted()
    {
        $this->assertSame('http://service.example.com?url=http%3A%2F%2Fwww.example.com',
            Share::load('http://www.example.com', null, null)->service());
    }

    public function testSeparator()
    {
        config(['social-share.separator' => '&amp;']);

        $this->assertSame('http://service.example.com?url=http%3A%2F%2Fwww.example.com&amp;title=Example',
            Share::load('http://www.example.com', 'Example')->service());
        $this->assertSame('http://service2.example.com?url=http%3A%2F%2Fwww.example.com&amp;extra1=Extra%201&amp;extra2=Extra%202',
            Share::load('http://www.example.com')->service2());
    }

    public function testEmailHonoursSeparator()
    {
        config(['social-share.separator' => '&amp;']);

        $this->assertSame('mailto:?subject=Example&amp;body=http%3A%2F%2Fwww.example.com',
            Share::load('http://www.example.com', 'Example')->email());
    }

    public function testUriWithExistingQueryString()
    {
        config(['social-share.services.query' => ['uri' => 'https://q.example.com/share?a=1&b=2']]);
        config(['social-share.services.trailing' => ['uri' => 'https://q.example.com/share?']]);

        $this->assertSame('https://q.example.com/share?a=1&b=2&url=http%3A%2F%2Fwww.example.com&title=Example',
            Share::load('http://www.example.com', 'Example')->query());
        $this->assertSame('https://q.example.com/share?url=http%3A%2F%2Fwww.example.com',
            Share::load('http://www.example.com')->trailing());
    }

    public function testTextServicesWithoutTitleHaveNoLeadingSpace()
    {
        $this->assertSame('whatsapp://send?text=http%3A%2F%2Fwww.example.com',
            Share::load('http://www.example.com')->whatsapp());
        $this->assertSame('https://bsky.app/intent/compose?text=http%3A%2F%2Fwww.example.com',
            Share::load('http://www.example.com')->bluesky());
    }

    public function testOnlyLimitsSharedValues()
    {
        config(['social-share.services.titleOnly' => ['uri' => 'https://o.example.com', 'only' => ['title']]]);
        config(['social-share.services.mailTitle' => ['view' => 'social-share::email', 'only' => ['title', 'app']]]);

        $this->assertSame('https://o.example.com?title=Example',
            Share::load('http://www.example.com', 'Example')->titleOnly());
        $this->assertSame('mailto:?subject=Example&body=',
            Share::load('http://www.example.com', 'Example')->mailTitle());
    }

    public function testCustomViewReceivesServiceSeparatorAndValues()
    {
        config(['social-share.services.custom' => ['view' => 'custom-share']]);

        View::shouldReceive('make')
            ->once()
            ->with('custom-share', [
                'service' => ['view' => 'custom-share'],
                'sep' => '&',
                'url' => 'http://www.example.com',
                'title' => 'Example',
                'media' => '',
            ])
            ->andReturn(Mockery::mock(['render' => " https://custom.example.com \n"]));

        $this->assertSame('https://custom.example.com', Share::load('http://www.example.com', 'Example')->custom());
    }

    public function testUnknownServiceThrows()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Share service [twiter] is not defined');

        Share::load('http://www.example.com')->twiter();
    }

    public function testHas()
    {
        $this->assertTrue(Share::has('twitter'));
        $this->assertFalse(Share::has('nope'));
    }

    public function testServices()
    {
        $services = array_keys(self::EXPECTED + self::CUSTOM);

        $this->assertSame(self::EXPECTED + self::CUSTOM,
            Share::load('http://www.example.com', 'Example', 'Media')->services(...$services));
    }

    public function testServicesWithArray()
    {
        $services = array_keys(self::EXPECTED + self::CUSTOM);

        $this->assertSame(self::EXPECTED + self::CUSTOM,
            Share::load('http://www.example.com', 'Example', 'Media')->services($services));
    }

    public function testServicesAsObject()
    {
        $expected = (object) ['twitter' => self::EXPECTED['twitter'], 'x' => self::EXPECTED['x']];

        $this->assertEquals($expected, Share::load('http://www.example.com', 'Example')->services('twitter', 'x', true));
        $this->assertEquals($expected, Share::load('http://www.example.com', 'Example')->services(['twitter', 'x'], true));
    }

    public function testDefaultIsAll()
    {
        $this->assertEquals(self::EXPECTED + self::CUSTOM,
            Share::load('http://www.example.com', 'Example', 'Media')->services());
    }

    public function testContainerBindings()
    {
        $this->assertInstanceOf(ShareManager::class, $this->app->make('share'));
        $this->assertSame($this->app->make('share'), $this->app->make(ShareManager::class));
    }
}
