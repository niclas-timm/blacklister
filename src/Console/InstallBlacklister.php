<?php

namespace NiclasTimm\Blacklister\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallBlacklister extends Command
{
    protected $signature = 'blacklister:install';

    protected $description = 'Install the Blacklister package';

    public function handle()
    {
        $this->info('Installing Blacklister...!');

        $this->info('Publishing configuration...');

        if (!$this->configExists()) {
            $this->publishConfiguration();
            $this->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration(forcePublish: true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        }

        $this->createBlacklistFile();

        $this->info('Installed Blacklister');
    }

    private function configExists(): bool
    {
        return File::exists(config_path('blacklister.php'));
    }

    private function publishConfiguration(bool $forcePublish = false): void
    {
        $params = [
            '--provider' => "NiclasTimm\Blacklister\BlacklisterServiceProvider",
            '--tag' => "config",
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }

    private function shouldOverwriteConfig(): bool
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function createBlacklistFile(): void
    {
        $path = config('blacklister.blacklist_path');

        if (file_exists($path)) {
            $this->line("Blacklist file already exists. Won't override");

            return;
        }

        $stub = file_get_contents(__DIR__.'/../../stubs/blacklist.json');

        file_put_contents($path, $stub);
        $this->info('Created blacklist file under '.$path);
    }
}