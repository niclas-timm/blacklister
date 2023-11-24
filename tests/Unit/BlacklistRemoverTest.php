<?php

namespace NiclasTimm\Blacklister\Tests\Unit;

use Illuminate\Support\Facades\Config;
use NiclasTimm\Blacklister\Blacklister;
use NiclasTimm\Blacklister\Tests\TestCase;

class BlacklistRemoverTest extends TestCase
{
    const TMP_BLACKLIST_PATH = __DIR__.'/../fixtures/blacklist_remover_test.json';

    protected Blacklister $blacklister;

    public function setUp(): void
    {
        parent::setUp();

        copy(__DIR__.'/../fixtures/blacklist.json', self::TMP_BLACKLIST_PATH);
        Config::set('blacklister.blacklist_path', self::TMP_BLACKLIST_PATH);

        $this->blacklister = app()->make(Blacklister::class);
    }

    /** @test */
    public function it_can_remove_email_from_blacklist()
    {
        $valueToRemove = 'block@me.com';

        $this->assertTrue(in_array($valueToRemove, $this->blacklister->getBlacklistFromFile()['emails']));

        $this->artisan('blacklister:remove '.$valueToRemove)
            ->expectsConfirmation('Are you sure you want to remove these values from your blacklist?', 'yes')
            ->assertExitCode(0);

        $this->assertFalse(in_array($valueToRemove, $this->blacklister->getBlacklistFromFile()['emails']));
    }

    /** @test */
    public function it_can_remove_domain_from_blacklist()
    {
        $valueToRemove = 'i-will-block-you.com';

        $this->assertTrue(in_array($valueToRemove, $this->blacklister->getBlacklistFromFile()['domains']));

        $this->artisan('blacklister:remove '.$valueToRemove)
            ->expectsConfirmation('Are you sure you want to remove these values from your blacklist?', 'yes')
            ->assertExitCode(0);

        $this->assertFalse(in_array($valueToRemove, $this->blacklister->getBlacklistFromFile()['emails']));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (file_exists(self::TMP_BLACKLIST_PATH)) {
            unlink(self::TMP_BLACKLIST_PATH);
        }
    }
}