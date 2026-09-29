<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Query string separator
    |--------------------------------------------------------------------------
    |
    | Glue placed between query parameters. Use '&amp;' when printing links
    | unescaped ({!! !!}) into HTML attributes. Rendered buttons always use
    | '&' internally, since Blade escapes the href for you.
    |
    */

    'separator' => '&',

    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    |
    | Each service is either rendered by a Blade 'view', or built from:
    |   uri        base URL (may already contain a query string)
    |   urlName    parameter for the shared URL   (default: url)
    |   titleName  parameter for the title        (default: title)
    |   mediaName  parameter for the media link   (media is omitted if unset)
    |   extra      additional fixed parameters
    |   only       subset of ['url', 'title', 'media'] to send
    |   label      human readable name used by rendered buttons
    |
    */

    'services' => [
        'bluesky' => ['label' => 'Bluesky', 'view' => 'social-share::bluesky'],
        'delicious' => ['label' => 'Delicious', 'uri' => 'https://delicious.com/post'],
        'digg' => ['label' => 'Digg', 'uri' => 'https://digg.com/submit'],
        'email' => ['label' => 'Email', 'view' => 'social-share::email'],
        'evernote' => ['label' => 'Evernote', 'uri' => 'https://evernote.com/clip.action'],
        'facebook' => ['label' => 'Facebook', 'uri' => 'https://facebook.com/sharer/sharer.php', 'urlName' => 'u'],
        'gmail' => ['label' => 'Gmail', 'uri' => 'https://mail.google.com/mail/', 'urlName' => 'su', 'titleName' => 'body', 'extra' => [
            'view' => 'cm',
            'fs' => 1,
            'to' => '',
            'ui' => 2,
            'tf' => 1,
        ]],
        'linkedin' => ['label' => 'LinkedIn', 'uri' => 'https://linkedin.com/shareArticle', 'extra' => ['mini' => 'true']],
        'pinterest' => ['label' => 'Pinterest', 'uri' => 'https://pinterest.com/pin/create/button/', 'titleName' => 'description', 'mediaName' => 'media'],
        'reddit' => ['label' => 'Reddit', 'uri' => 'https://reddit.com/submit'],
        'telegramMe' => ['label' => 'Telegram', 'uri' => 'https://telegram.me/share/url', 'titleName' => 'text'],
        'threads' => ['label' => 'Threads', 'uri' => 'https://www.threads.net/intent/post', 'titleName' => 'text'],
        'tumblr' => ['label' => 'Tumblr', 'uri' => 'https://tumblr.com/share', 'urlName' => 'u', 'titleName' => 't', 'extra' => [
            'v' => 3,
        ]],
        'twitter' => ['label' => 'Twitter', 'uri' => 'https://twitter.com/intent/tweet', 'titleName' => 'text'],
        'vk' => ['label' => 'VK', 'uri' => 'https://vk.com/share.php', 'mediaName' => 'image', 'extra' => [
            'noparse' => 'false',
        ]],
        'whatsapp' => ['label' => 'WhatsApp', 'view' => 'social-share::whatsapp'],
        'x' => ['label' => 'X', 'uri' => 'https://x.com/intent/post', 'titleName' => 'text'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Button theme
    |--------------------------------------------------------------------------
    |
    | Markup used by Share::render() and <x-social-share::buttons>. Built in:
    | 'bootstrap', 'tailwind' and 'plain'. Any other value is treated as a
    | view name (e.g. 'share.buttons') which receives $buttons and $labels.
    |
    */

    'theme' => 'bootstrap',

    /*
    |--------------------------------------------------------------------------
    | Icon sets
    |--------------------------------------------------------------------------
    |
    | 'default' is used when no set is passed. Each set defines the stylesheet
    | printed by Share::styles(), a 'fallback' icon for services it has no
    | brand icon for, and the per-service icon classes. Add your own sets or
    | override individual icons here.
    |
    */

    'icons' => [
        'default' => 'fontawesome',

        'sets' => [
            'fontawesome' => [
                'stylesheet' => 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.3.1/css/all.min.css',
                'integrity' => 'sha384-qrALq7+6jBOZIQsNnT6xGkMDru64qD6uTlDra39xrt2SoXl4pO3FX6Roz/RpR/BS',
                'fallback' => 'fa-solid fa-share-nodes',
                'icons' => [
                    'bluesky' => 'fa-brands fa-bluesky',
                    'delicious' => 'fa-brands fa-delicious',
                    'digg' => 'fa-brands fa-digg',
                    'email' => 'fa-solid fa-envelope',
                    'evernote' => 'fa-brands fa-evernote',
                    'facebook' => 'fa-brands fa-facebook-f',
                    'gmail' => 'fa-brands fa-google',
                    'linkedin' => 'fa-brands fa-linkedin-in',
                    'pinterest' => 'fa-brands fa-pinterest-p',
                    'reddit' => 'fa-brands fa-reddit-alien',
                    'telegramMe' => 'fa-brands fa-telegram',
                    'threads' => 'fa-brands fa-threads',
                    'tumblr' => 'fa-brands fa-tumblr',
                    'twitter' => 'fa-brands fa-twitter',
                    'vk' => 'fa-brands fa-vk',
                    'whatsapp' => 'fa-brands fa-whatsapp',
                    'x' => 'fa-brands fa-x-twitter',
                ],
            ],

            'lineawesome' => [
                'stylesheet' => 'https://cdn.jsdelivr.net/npm/line-awesome@1.3.0/dist/line-awesome/css/line-awesome.min.css',
                'integrity' => 'sha384-h0HFp1r+Vq/1x7modzqZaqvMelro9pikGIVdLDs045XCE8IfHpIQDcGPiBNslfXT',
                'fallback' => 'las la-share-alt',
                'icons' => [
                    'delicious' => 'lab la-delicious',
                    'digg' => 'lab la-digg',
                    'email' => 'las la-envelope',
                    'evernote' => 'lab la-evernote',
                    'facebook' => 'lab la-facebook-f',
                    'gmail' => 'lab la-google',
                    'linkedin' => 'lab la-linkedin-in',
                    'pinterest' => 'lab la-pinterest-p',
                    'reddit' => 'lab la-reddit-alien',
                    'telegramMe' => 'lab la-telegram',
                    'tumblr' => 'lab la-tumblr',
                    'twitter' => 'lab la-twitter',
                    'vk' => 'lab la-vk',
                    'whatsapp' => 'lab la-whatsapp',
                ],
            ],

            'bootstrap-icons' => [
                'stylesheet' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css',
                'integrity' => 'sha384-CK2SzKma4jA5H/MXDUU7i1TqZlCFaD4T01vtyDFvPlD97JQyS+IsSh1nI2EFbpyk',
                'fallback' => 'bi bi-share-fill',
                'icons' => [
                    'bluesky' => 'bi bi-bluesky',
                    'email' => 'bi bi-envelope',
                    'facebook' => 'bi bi-facebook',
                    'gmail' => 'bi bi-google',
                    'linkedin' => 'bi bi-linkedin',
                    'pinterest' => 'bi bi-pinterest',
                    'reddit' => 'bi bi-reddit',
                    'telegramMe' => 'bi bi-telegram',
                    'threads' => 'bi bi-threads',
                    'twitter' => 'bi bi-twitter',
                    'whatsapp' => 'bi bi-whatsapp',
                    'x' => 'bi bi-twitter-x',
                ],
            ],
        ],
    ],

];
