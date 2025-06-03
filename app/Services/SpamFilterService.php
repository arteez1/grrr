<?php

namespace App\Services;

use App\Models\SpamFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SpamFilterService
{

    protected Collection $activeFilters;

    public function __construct()
    {
        // Кэшируем фильтры на 5 минут для производительности
        $this->activeFilters = Cache::remember('spam_filters', 300, function () {
            return SpamFilter::where('is_active', true)->get();
        });
    }

    public function isSpam(string $content, ?string $ip = null, ?int $userId = null): bool
    {
        return $this->checkKeywords($content)
            || $this->checkIp($ip)
            || $this->checkUserId($userId)
            || $this->checkRegexPatterns($content);
    }

    // Проверка ключевых слов (без учета регистра)
    protected function checkKeywords(string $content): bool
    {
        $keywords = $this->activeFilters
            ->where('type', 'keyword')
            ->pluck('value');

        return $keywords->contains(fn ($keyword) =>
            stripos($content, $keyword) !== false
        );
    }


    // Проверка IP (поддержка CIDR-диапазонов)
    protected function checkIp(?string $ip): bool
    {
        if (!$ip) return false;

        return $this->activeFilters
            ->where('type', 'ip')
            ->contains(fn ($filter) =>
            $this->matchIp($ip, $filter->value)
            );
    }


    // Проверка ID пользователя
    protected function checkUserId(?int $userId): bool
    {
        if (!$userId) return false;

        return $this->activeFilters
            ->where('type', 'user_id')
            ->pluck('value')
            ->contains($userId);
    }


    // Новый тип: проверка по регулярным выражениям
    protected function checkRegexPatterns(string $content): bool
    {
        return $this->activeFilters
            ->where('type', 'regex')
            ->pluck('value')
            ->contains(fn ($pattern) =>
            preg_match("/{$pattern}/i", $content)
            );
    }


    // Проверка IP с учетом CIDR (например, 192.168.1.0/24)
    protected function matchIp(string $ip, string $pattern): bool
    {
        if (strpos($pattern, '/') !== false) {
            list($subnet, $mask) = explode('/', $pattern);
            return $this->ipInRange($ip, $subnet, $mask);
        }

        return $ip === $pattern;
    }


    protected function ipInRange(string $ip, string $subnet, int $mask): bool
    {
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $mask = ~((1 << (32 - $mask)) - 1);

        return ($ipLong & $mask) === ($subnetLong & $mask);
    }
}
