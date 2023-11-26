<?php

namespace NiclasTimm\Blacklister\Tests\Unit;

use Illuminate\Support\Facades\Config;
use NiclasTimm\Blacklister\Tests\TestCase;

class ImportBlacklistTest extends TestCase
{

    const TMP_BLACKLIST_PATH = __DIR__.'/../fixtures/import_blacklist_default.json';

    public function setUp(): void
    {
        parent::setUp();
        copy(__DIR__.'/../fixtures/blacklist.json', self::TMP_BLACKLIST_PATH);
        Config::set('blacklister.blacklist_path', self::TMP_BLACKLIST_PATH);
    }

    /** @test */
    public function it_can_import_a_blacklist_from_csv()
    {
        $file = __DIR__.'/../fixtures/import_data.csv';
        $this->artisan('blacklister:import '.$file);

        $newBlacklistContent = file_get_contents($file);
        $this->assertTrue(str_contains($newBlacklistContent, 'gmail.com'));
        $this->assertTrue(str_contains($newBlacklistContent, 'test3@mgail.fr'));
    }

    public function tearDown(): void
    {
        unlink(self::TMP_BLACKLIST_PATH);
    }
}