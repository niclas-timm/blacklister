<?php

namespace NiclasTimm\Blacklister\Tests\Unit;

use NiclasTimm\Blacklister\Tests\TestCase;

class BlacklistExportTest extends TestCase
{
    /** @test */
    public function it_can_export_the_blacklist()
    {
        $path = __DIR__.'/../fixtures/export.csv';
        $this->artisan('blacklister:export --filename="'.$path.'"')->assertOk();
        $this->assertTrue(file_exists($path));
        unlink($path);
    }

    /** @test */
    public function it_can_overwrite_existing_file()
    {
        $path = __DIR__.'/../fixtures/export_overwrite.csv';
        $file = fopen($path, 'w');
        fclose($file);
        $content = file_get_contents($path);
        $this->assertTrue($content === '');
        $this->artisan('blacklister:export --filename="'.$path.'"')
            ->expectsConfirmation('File already exist. Overwrite?', 'yes')
            ->assertOk();
        $content = file_get_contents($path);
        $this->assertTrue($content !== '');
        unlink($path);
    }
}