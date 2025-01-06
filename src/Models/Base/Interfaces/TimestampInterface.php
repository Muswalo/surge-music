<?php

namespace Muswalo\Surgemusic\Models\Base\Interfaces;

interface TimestampInterface
{
    public function getCreatedAt(): string;
    public function getUpdatedAt(): string;
    public function setUpdatedAt(): void;
    public function touchTimestamp(): void;
}
