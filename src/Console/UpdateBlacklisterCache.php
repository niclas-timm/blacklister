<?php

namespace NiclasTimm\Blacklister\Console;

use Illuminate\Console\Command;
use NiclasTimm\Blacklister\Blacklister;

class UpdateBlacklisterCache extends Command
{
    protected $signature = 'blacklister:update-cache';

    protected $description = 'Update the Blacklister cache';

    public function __construct(protected Blacklister $blacklister)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        if (!$this->blacklister->isCacheEnabled()) {
            $this->error('You disabled caching for blacklister. Therefore, this command does nothing.');
        }

        $this->line('Invalidating cache...');
        $this->blacklister->invalidateCache();

        $this->line('Caching blacklist...');
        $this->blacklister->cacheBlacklist();

        $this->info('Done. Ready to blacklist stuff! 🎉');
    }
}