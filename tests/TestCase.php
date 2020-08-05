<?php

namespace Tests;

use Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * set up.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate', [
            '-vvv' => true,
        ]);
        Artisan::call('passport:install', [
            '-vvv' => true,
        ]);
        Artisan::call('db:seed', [
            '-vvv' => true,
        ]);
    }
}
