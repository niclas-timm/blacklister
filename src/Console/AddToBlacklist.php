<?php

namespace NiclasTimm\Blacklister\Console;

use Illuminate\Console\Command;
use NiclasTimm\Blacklister\Blacklister;

class AddToBlacklist extends Command
{
    protected $signature = 'blacklister:add {values*} {--T|type=emails}';

    protected $description = 'Add a new email or domain to the blacklist';

    public function __construct(protected Blacklister $blacklister)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $type = $this->option('type');
        if (!$this->isAllowedType($type)) {
            $this->error('The provided type is not allowed. It must be one of these: '.implode(', ',
                    $this->allowedTypes()));

            return;
        }

        $values = $this->argument('values');

        $blacklist = $this->blacklister->getBlacklistFromFile();

        $exisingValues = $blacklist[$type];

        $newValues = array_merge($exisingValues, $values);

        $blacklist[$type] = $newValues;

        $this->blacklister->overwriteBlacklistFile($blacklist);

        $this->blacklister->cacheBlacklist();

        $this->info('Blacklist updated. 🎉');

        $this->warn('⚠️  Make sure to run `php artisan blacklister:update-cache` on your next deployment to cache the new values.  ⚠️');
    }

    private function isAllowedType(string $type): string
    {
        return in_array($type, $this->allowedTypes());
    }

    private function allowedTypes(): array
    {
        return ['emails', 'domains'];
    }
}