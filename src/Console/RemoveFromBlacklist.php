<?php

namespace NiclasTimm\Blacklister\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use NiclasTimm\Blacklister\Blacklister;

class RemoveFromBlacklist extends Command
{
    protected $signature = 'blacklister:remove {values*}';

    protected $description = 'Remove a value from blacklister';

    public function __construct(protected Blacklister $blacklister)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        if (!$this->confirm('Are you sure you want to remove these values from your blacklist?')) {
            $this->error('Aborted');
            exit(1);
        }

        $values = $this->argument('values');

        $blacklist = $this->blacklister->getBlacklistFromFile();
        $updatedBlacklist = $blacklist;

        foreach ($values as $value) {
            $type = $this->getBlacklistKeyType($value);

            $key = array_search($value, $updatedBlacklist[$type]);

            if ($key === false) {
                $this->error('Could not find '.$value.' in '.$type);
                continue;
            }

            unset($updatedBlacklist[$type][$key]);

            $this->blacklister->overwriteBlacklistFile($updatedBlacklist);
            $this->blacklister->invalidateCache();

            $this->info('Removed '.$value.' from '.$type);
        }
    }

    private function getBlacklistKeyType(string $value): string
    {
        return $this->isEmail($value)
            ? 'emails'
            : 'domains';
    }

    private function isEmail(string $value): bool
    {
        return Str::contains($value, '@');
    }
}
