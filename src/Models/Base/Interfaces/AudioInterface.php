<?php

namespace Muswalo\Surgemusic\Models\Base\Interfaces;
use Muswalo\Surgemusic\Models\Base\Interfaces\FileHandlingInterface;

interface AudioInterface extends FileHandlingInterface 
{
    public function getAudioFeatures(): array;
    public function getDuration(): int;
    public function getBitrate(): int;
    public function getFormat(): string;
}