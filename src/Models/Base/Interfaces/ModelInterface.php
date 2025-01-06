<?php

namespace Muswalo\Surgemusic\Models\Base\Interfaces;

interface ModelInterface
{
    public function find(int $id): ?self; 
    public function create(array $data): self;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function save(): bool;
    public function toArray(): array;
    public function fill(array $attributes): self;
    public function getDirty(): array;
}
