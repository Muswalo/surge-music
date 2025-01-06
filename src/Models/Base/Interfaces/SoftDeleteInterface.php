<?php

namespace Muswalo\Surgemusic\Models\Base\Interfaces;

interface SoftDeleteInterface
{
    public function softDelete(): bool;
    public function restore(): bool;
    public function forceDelete(int $id): bool;
    public function isSoftDeleted(): bool;
}
