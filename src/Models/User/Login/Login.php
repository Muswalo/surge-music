<?php

namespace Muswalo\Surgemusic\Models\User\Login;

use Muswalo\Surgemusic\Models\Base\Model;

/**
 * Login Model
 * 
 * @property int $id
 * @property int $user_id
 * @property string $ip_address
 * @property string $user_agent
 * @property string $location
 * @property bool $is_valid
 * @property string $created_at
 * @property string $updated_at
 * @property string $expires_at
 * @property string $deleted_at
 * 
 */
class Login extends Model
{
    protected string $table = 'logins';
    protected array $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'location',
        'is_valid',
        'created_at',
        'updated_at',
        'expires_at',
        'deleted_at',
    ];

}
