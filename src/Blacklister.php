<?php

namespace NiclasTimm\Blacklister;

use Illuminate\Support\Facades\Cache;

class Blacklister
{
    private bool $cacheEnabled;

    private string $cacheKey;

    private int $cacheTTL;

    private string $blacklistPath;

    private string $validationMessage;

    public function __construct()
    {
        $this->cacheEnabled = config('blacklister.enable_cache');
        $this->cacheKey = config('blacklister.cache_key', '');
        $this->cacheTTL = (int) config('blacklister.cache_ttl', 0);
        $this->blacklistPath = config('blacklister.blacklist_path', '');
        $this->validationMessage = config('blacklister.validation_message', '');
    }

    public function getBlacklist(): array
    {
        if (!$this->cacheEnabled) {
            return $this->getBlacklistFromFile();
        }

        if (!Cache::has($this->cacheKey)) {
            $this->cacheBlacklist();
        }

        return Cache::get($this->cacheKey, []);
    }

    public function getBlacklistFromFile(): array
    {
        return json_decode(file_get_contents($this->blacklistPath), true);
    }

    public function cacheBlacklist(): void
    {
        $json = $this->getBlacklistFromFile();

        Cache::put($this->cacheKey, $json, now()->addMinutes($this->cacheTTL));
    }

    public function invalidateCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    public function isCacheEnabled(): bool
    {
        return $this->cacheEnabled;
    }

    public function blacklistExists(): bool
    {
        if (!file_exists($this->blacklistPath)) {
            return false;
        }

        return !!json_decode(file_get_contents($this->blacklistPath), true);
    }

    public function hasValidStructure(): bool
    {
        $content = $this->getBlacklistFromFile();

        $requiredKeys = ['emails', 'domains'];

        $hasErrors = false;
        foreach ($requiredKeys as $key) {
            if (!isset($content[$key]) || !is_array($content[$key])) {
                $hasErrors = true;
                break;
            }
        }

        return !$hasErrors;
    }

    public function hasValidCacheKey(): bool
    {
        return !empty($this->cacheKey);
    }

    public function hasValidCacheTtl(): bool
    {
        return !empty($this->cacheTTL);
    }

    public function hasValidValidationMessage(): bool
    {
        return !empty($this->validationMessage);
    }
}