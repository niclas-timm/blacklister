<?php

namespace NiclasTimm\Blacklister;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class Blacklister
{
    private bool $cacheEnabled;

    private string $cacheKey;

    private int $cacheTTL;

    private string $blacklistPath;

    private string $validationMessage;

    private bool $cookiesEnabled;

    private string $cookieName;

    private int $cookieTTL;

    public function __construct()
    {
        $this->cacheEnabled = config('blacklister.enable_cache');
        $this->cacheKey = config('blacklister.cache_key', '');
        $this->cacheTTL = (int) config('blacklister.cache_ttl', 0);
        $this->blacklistPath = config('blacklister.blacklist_path', '');
        $this->validationMessage = config('blacklister.validation_message', '');
        $this->cookiesEnabled = config('blacklister.enable_cookies', false);
        $this->cookieName = config('blacklister.cookie_name', 'blacklister');
        $this->cookieTTL = (int) config('blacklister.cookie_ttl', 60 * 24 * 30);
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

    public function overwriteBlacklistFile(array $newBlacklist): void
    {
        file_put_contents($this->blacklistPath, json_encode($newBlacklist));
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

    public function hasBlockingCookie(): bool
    {
        return $this->cookiesEnabled && request()->cookies->has($this->cookieName);
    }

    public function setBlockingCookie(): void
    {
        if ($this->areCookiesEnabled()) {
            Cookie::queue($this->cookieName, 'blocked', $this->cookieTTL);
        }
    }

    public function areCookiesEnabled(): bool
    {
        return $this->cookiesEnabled;
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