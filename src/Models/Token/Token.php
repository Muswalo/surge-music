<?php

namespace Muswalo\Surgemusic\Models\Token;

use Muswalo\Surgemusic\Models\Base\Model;

class Token extends Model
{
    protected string $table = 'verification_codes';
    protected array $fillable = [
        'user_id', 
        'code',
        'created_at', 
        'expires_at',
        'updated_at',
        'deleted_at',
        'is_verified',
    ];


    public function getCode(): string
    {
        return $this->attributes['code'] ?? '';
    }


    public function getIsVerified(): bool
    {
        return $this->attributes['is_verified'] ?? false;
    }

    public function setVerified(): void
    {
        $this->attributes['is_verified'] = true;
        $this->save();
    }

}
