<?php

use Siberfx\Share\Facade\Share;
use Siberfx\Share\ShareServiceProvider;

class TestCase extends Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [ShareServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return ['Share' => Share::class];
    }
}
