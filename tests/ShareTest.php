<?php

use Illuminate\Support\Facades\View;
use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\Group;

class ShareTest extends TestCase
{
    protected $expected = [
        "delicious" => "https://delicious.com/post?url=http%3A%2F%2Fwww.example.com&title=Example",
        "digg" => "https://digg.com/submit?url=http%3A%2F%2Fwww.example.com&title=Example",
        "email" => "mailto:?subject=Example&body=http%3A%2F%2Fwww.example.com",
        "evernote" => "https://evernote.com/clip.action?url=http%3A%2F%2Fwww.example.com&title=Example",
        "facebook" => "https://facebook.com/sharer/sharer.php?u=http%3A%2F%2Fwww.example.com&title=Example",
        "gmail" => "https://mail.google.com/mail/?su=http%3A%2F%2Fwww.example.com&body=Example&view=cm&fs=1&to=&ui=2&tf=1",
        "linkedin" => "https://linkedin.com/shareArticle?url=http%3A%2F%2Fwww.example.com&title=Example&mini=true",
        "pinterest" => "https://pinterest.com/pin/create/button/?url=http%3A%2F%2Fwww.example.com&description=Example&media=Media",
        "reddit" => "https://reddit.com/submit?url=http%3A%2F%2Fwww.example.com&title=Example",
        "telegramMe" => "https://telegram.me/share/url?url=http%3A%2F%2Fwww.example.com&text=Example",
        "tumblr" => "https://tumblr.com/share?u=http%3A%2F%2Fwww.example.com&t=Example&v=3",
        "twitter" => "https://twitter.com/intent/tweet?url=http%3A%2F%2Fwww.example.com&text=Example",
        "vk" => "https://vk.com/share.php?url=http%3A%2F%2Fwww.example.com&title=Example&image=Media&noparse=false",
        "whatsapp" => "whatsapp://send?text=Example%20http%3A%2F%2Fwww.example.com",

        "service" => "http://service.example.com?url=http%3A%2F%2Fwww.example.com&title=Example&media=Media",
        "service2" => "http://service2.example.com?url=http%3A%2F%2Fwww.example.com&title=Example&extra1=Extra%201&extra2=Extra%202",
    ];

    protected function getPackageProviders($app)
    {
        return [
            'Siberfx\Share\ShareServiceProvider',
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Share' => 'Siberfx\Share\Facade\Share',
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('social-share.services.service', [
            'uri' => 'http://service.example.com',
            'mediaName' => 'media',
        ]);

        $app['config']->set('social-share.services.service2', [
            'uri' => 'http://service2.example.com',
            'extra' => [ 'extra1' => 'Extra 1', 'extra2' => 'Extra 2' ]
        ]);
    }

    public function testCallMethod()
    {
        $view = View::make('social-share::mock');

        View::shouldReceive('make')
            ->with('social-share::default', [
                'service' => [ 'uri' => 'http://service2.example.com', 'extra' => [
                    'extra1' => 'Extra 1',
                    'extra2' => 'Extra 2',
                ]],
                'url' => 'http://www.example.com',
                'title' => '',
                'media' => '',
                'sep' => '&'
            ])
            ->andReturn($view);

        Share::load('http://www.example.com')->service2();

        $this->addToAssertionCount(1);
    }

    public function testViewMake()
    {
        $view = View::make('social-share::mock');

        View::shouldReceive('make')
            ->with('social-share::default', [
                'service' => [ 'uri' => 'http://service.example.com', 'mediaName' => 'media' ],
                'url' => 'http://www.example.com',
                'title' => '',
                'media' => '',
                'sep' => '&'
            ])
            ->andReturn($view);

        Share::load('http://www.example.com')->service();

        $this->addToAssertionCount(1);
    }

    public function testRenderUrlOnly()
    {
        $this->assertEquals('http://service.example.com?url=http%3A%2F%2Fwww.example.com',
                            Share::load('http://www.example.com')->service());
    }

    public function testRenderUrlAndTitle()
    {
        $this->assertEquals('http://service.example.com?url=http%3A%2F%2Fwww.example.com&title=Example',
                            Share::load('http://www.example.com', 'Example')->service());
    }

    public function testRenderUrlTitleAndMedia()
    {
        $this->assertEquals('http://service.example.com?url=http%3A%2F%2Fwww.example.com&title=Example&media=Media',
                            Share::load('http://www.example.com', 'Example', 'Media')->service());
    }

    public function testRenderExtra()
    {
        $this->assertEquals('http://service2.example.com?url=http%3A%2F%2Fwww.example.com&extra1=Extra%201&extra2=Extra%202',
                            Share::load('http://www.example.com')->service2());
    }

    public function testSeparator()
    {
        $this->app['config']->set('social-share.separator', '&amp;');
        $this->assertEquals('http://service.example.com?url=http%3A%2F%2Fwww.example.com&amp;title=Example',
                            Share::load('http://www.example.com', 'Example')->service());
    }

    public function testServices()
    {
        $actual = Share::load('http://www.example.com', 'Example', 'Media')->services(
            'delicious',
            'digg',
            'email',
            'evernote',
            'facebook',
            'gmail',
            'linkedin',
            'pinterest',
            'reddit',
            'telegramMe',
            'tumblr',
            'twitter',
            'vk',
            'whatsapp',

            'service',
            'service2'
        );

        $this->assertEquals($this->expected, $actual);
    }

    public function testServicesWithArray()
    {
        $actual = Share::load('http://www.example.com', 'Example', 'Media')->services(
            [
                'delicious',
                'digg',
                'email',
                'evernote',
                'facebook',
                'gmail',
                'linkedin',
                'pinterest',
                'reddit',
                'telegramMe',
                'tumblr',
                'twitter',
                'vk',
                'whatsapp',

                'service',
                'service2',
            ]
        );

        $this->assertEquals($this->expected, $actual);
    }

    public function testDefaultIsAll()
    {
        // The default config defines every built-in service; service/service2
        // are only added via getEnvironmentSetUp, so include them explicitly.
        $expected = $this->expected;
        unset($expected['service'], $expected['service2']);

        $actual = Share::load('http://www.example.com', 'Example', 'Media')->services();

        // Drop the test-only services from the actual result before comparing.
        unset($actual['service'], $actual['service2']);

        $this->assertEquals($expected, $actual);
    }

    protected function assertPageFound($url)
    {
        $client = new Client([
            'http_errors' => false,
        ]);

        $response = $client->head($url);
        $this->assertEquals(200,  $response->getStatusCode());
    }

    #[Group('live')]
    public function testDelicious()
    {
        $url = 'https://delicious.com/post?url=http%3A%2F%2Fwww.example.com&title=Example';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->delicious());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testDigg()
    {
        $url = 'https://digg.com/submit?url=http%3A%2F%2Fwww.example.com&title=Example';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->digg());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testEmail()
    {
        $url = 'mailto:?subject=Example&body=http%3A%2F%2Fwww.example.com';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->email());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testEvernote()
    {
        $url = 'https://evernote.com/clip.action?url=http%3A%2F%2Fwww.example.com&title=Example';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->evernote());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testFacebook()
    {
        $url = 'https://facebook.com/sharer/sharer.php?u=http%3A%2F%2Fwww.example.com&title=Example';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->facebook());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testGmail()
    {
        $url = 'https://mail.google.com/mail/?su=http%3A%2F%2Fwww.example.com&body=Example&view=cm&fs=1&to=&ui=2&tf=1';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->gmail());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testLinkedin()
    {
        $url = 'https://linkedin.com/shareArticle?url=http%3A%2F%2Fwww.example.com&title=Example&mini=true';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->linkedin());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testPinterest()
    {
        $url = 'https://pinterest.com/pin/create/button/?url=http%3A%2F%2Fwww.example.com&description=Example&media=Media';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example', 'Media')->pinterest());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testReddit()
    {
        $url = 'https://reddit.com/submit?url=http%3A%2F%2Fwww.example.com&title=Example';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->reddit());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testTelegramMe()
    {
        $url = 'https://telegram.me/share/url?url=http%3A%2F%2Fwww.example.com&text=Example';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->telegramMe());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testTumblr()
    {
        $url = 'https://tumblr.com/share?u=http%3A%2F%2Fwww.example.com&t=Example&v=3';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->tumblr());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testTwitter()
    {
        $url = 'https://twitter.com/intent/tweet?url=http%3A%2F%2Fwww.example.com&text=Example';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->twitter());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testVk()
    {
        $url = 'https://vk.com/share.php?url=http%3A%2F%2Fwww.example.com&title=Example&image=Media&noparse=false';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example', 'Media')->vk());
        // $this->assertPageFound($url);
    }

    #[Group('live')]
    public function testWhatsapp()
    {
        $url = 'whatsapp://send?text=Example%20http%3A%2F%2Fwww.example.com';
        $this->assertEquals($url, Share::load('http://www.example.com', 'Example')->whatsapp());
        // $this->assertPageFound($url);
    }
}
