<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Query string separator
    |--------------------------------------------------------------------------
    |
    | Glue placed between query parameters. Use '&amp;' when printing links
    | unescaped ({!! !!}) into HTML attributes.
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
    |
    */

    'services' => [
        'bluesky' => ['view' => 'social-share::bluesky'],
        'delicious' => ['uri' => 'https://delicious.com/post'],
        'digg' => ['uri' => 'https://digg.com/submit'],
        'email' => ['view' => 'social-share::email'],
        'evernote' => ['uri' => 'https://evernote.com/clip.action'],
        'facebook' => ['uri' => 'https://facebook.com/sharer/sharer.php', 'urlName' => 'u'],
        'gmail' => ['uri' => 'https://mail.google.com/mail/', 'urlName' => 'su', 'titleName' => 'body', 'extra' => [
            'view' => 'cm',
            'fs' => 1,
            'to' => '',
            'ui' => 2,
            'tf' => 1,
        ]],
        'linkedin' => ['uri' => 'https://linkedin.com/shareArticle', 'extra' => ['mini' => 'true']],
        'pinterest' => ['uri' => 'https://pinterest.com/pin/create/button/', 'titleName' => 'description', 'mediaName' => 'media'],
        'reddit' => ['uri' => 'https://reddit.com/submit'],
        'telegramMe' => ['uri' => 'https://telegram.me/share/url', 'titleName' => 'text'],
        'threads' => ['uri' => 'https://www.threads.net/intent/post', 'titleName' => 'text'],
        'tumblr' => ['uri' => 'https://tumblr.com/share', 'urlName' => 'u', 'titleName' => 't', 'extra' => [
            'v' => 3,
        ]],
        'twitter' => ['uri' => 'https://twitter.com/intent/tweet', 'titleName' => 'text'],
        'vk' => ['uri' => 'https://vk.com/share.php', 'mediaName' => 'image', 'extra' => [
            'noparse' => 'false',
        ]],
        'whatsapp' => ['view' => 'social-share::whatsapp'],
        'x' => ['uri' => 'https://x.com/intent/post', 'titleName' => 'text'],
    ],

];
