<?php
namespace Muswalo\Surgemusic\Models\Base\Interfaces;


interface SearchableInterface 
{
    public function search(string $term): array;
    public function searchBy(array $criteria): array;
    public function getSearchableFields(): array;
}