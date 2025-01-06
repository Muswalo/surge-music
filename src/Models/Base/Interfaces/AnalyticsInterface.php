<?php

namespace Muswalo\Surgemusic\Models\Base\Interfaces;

interface AnalyticsInterface 
{
    public function track(string $event, array $data): bool;
    public function getStats(string $period = 'day'): array;
    public function generateReport(string $type, array $params = []): array;
}