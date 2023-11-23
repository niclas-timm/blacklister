<?php

namespace NiclasTimm\Blacklister\Tests\Unit;

use Illuminate\Support\Facades\Config;
use NiclasTimm\Blacklister\Tests\TestCase;

class ValidationTest extends TestCase
{
    /** @test */
    public function it_blocks_blacklisted_domains()
    {
        Config::set('blacklister.blacklist_path', __DIR__.'/../fixtures/blacklist.json');

        $this->post('/test', [
            'email' => 'test@block-me.com',
        ])->assertInvalid();

        $this->post('/test', [
            'email' => 'test@i-will-block-you.com',
        ])->assertInvalid();
    }

    /** @test */
    public function it_blocks_blacklisted_emails()
    {
        Config::set('blacklister.blacklist_path', __DIR__.'/../fixtures/blacklist.json');

        $this->post('/test', [
            'email' => 'block@me.com',
        ])->assertInvalid();

        $this->post('/test', [
            'email' => 'block@you.com',
        ])->assertInvalid();
    }

    /** @test */
    public function it_does_not_block_non_blacklisted_emails_or_domains()
    {
        Config::set('blacklister.blacklist_path', __DIR__.'/../fixtures/blacklist.json');

        $this->post('/test', [
            'email' => 'test@not-blacklisted.com',
        ])->assertOk();

        $this->post('/test', [
            'email' => 'me@i-am-not-dangerous.com',
        ])->assertOk();
    }
}