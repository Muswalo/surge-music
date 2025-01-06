<?php

namespace Muswalo\Surgemusic\Models\User;

use Muswalo\Surgemusic\Models\Base\Model;

/**
 * User model
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $email
 * @property string $phone
 * @property string $image
 * @property string $password
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $verified_at
 * @property bool $is_verified
 * @property bool $is_agreed_to_terms
 * @property string $google_id
 * @property string $provider
 * 
 */
class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = [
        'first_name', 
        'last_name', 
        'username', 
        'email', 
        'phone', 
        'image', 
        'password', 
        'created_at', 
        'updated_at', 
        'deleted_at', 
        'verified_at', 
        'is_verified', 
        'is_agreed_to_terms', 
        'google_id', 
        'provider'
    ];


    public function isVerified(): bool
    {
        return $this->getAttribute("is_verified") ?? false;
    }

}
