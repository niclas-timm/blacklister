<?php

namespace NiclasTimm\Blacklister;

use Illuminate\Support\Facades\Cache;

class Validator
{
    private bool $cacheEnabled;

    private string $cacheKey;

    private int $cacheTTL;

    private string $blacklistPath;

    public function __construct()
    {
        $this->cacheEnabled = config('blacklister.cache_enabled');
        $this->cacheKey = config('blacklister.cache_key');
        $this->cacheTTL = (int) config('blacklister.cache_ttl');
        $this->blacklistPath = config('blacklister.blacklist_path');
    }

    public function validate($attribute, $value, $parameters): bool
    {
        $blacklist = $this->getBlacklist();

        if (in_array($this->getDomain($value), $blacklist['domains'])) {
            return false;
        }

        return !in_array($value, $blacklist['emails']);
    }

    private function getBlacklist(): array
    {
        if (!$this->cacheEnabled) {
            return $this->getBlacklistFromFile();
        }

        if (!Cache::has($this->cacheKey)) {
            $this->cacheBlacklist();
        }

        return Cache::get($this->cacheKey, []);
    }

    private function getBlacklistFromFile(): array
    {
        return json_decode(file_get_contents($this->blacklistPath), true);
    }

    private function cacheBlacklist(): void
    {
        $json = $this->getBlacklistFromFile();

        Cache::put($this->cacheKey, $json, now()->addMinutes($this->cacheTTL));
    }

    private function getDomain(string $value): string
    {
        return explode('@', $value)[1];
    }

    public function message($message, $attribute, $rule, $parameters)
    {
        return __('The domain for :attribute is not allowed. Please use another email address.',
            ['attribute' => $attribute]);
    }
}