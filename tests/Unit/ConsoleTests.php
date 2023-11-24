<?php

namespace NiclasTimm\Blacklister\Tests\Unit;

use NiclasTimm\Blacklister\Tests\TestCase;

class ConsoleTests extends TestCase
{

    /** @test */
    public function it_can_view_the_blacklist()
    {
        $this->artisan('blacklister:view')
            ->expectsOutputToContain('block@you.com')
            ->assertExitCode(0);
    }
}