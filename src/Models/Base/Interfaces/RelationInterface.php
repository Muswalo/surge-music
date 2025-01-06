<?php
namespace Muswalo\Surgemusic\Models\Base\Interfaces;

interface RelationInterface 
{
    public function hasOne(string $relatedModel, string $foreignKey, string $localKey): ?object;
    public function hasMany(string $relatedModel, string $foreignKey, string $localKey): array;
    public function belongsTo(string $relatedModel, string $foreignKey, string $ownerKey): ?object;
    public function belongsToMany(string $relatedModel, string $pivotTable, string $foreignKey, string $relatedKey): array;
}
