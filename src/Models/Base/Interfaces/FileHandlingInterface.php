<?php

namespace Muswalo\Surgemusic\Models\Base\Interfaces;


interface FileHandlingInterface 
{
    public function uploadFile(array $file, string $destination): string;
    public function deleteFile(string $path): bool;
    public function getFilePath(): string;
    public function validateFile(array $file): bool;
}
