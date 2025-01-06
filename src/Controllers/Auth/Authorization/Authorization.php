<?php

namespace Muswalo\Surgemusic\Controllers\Auth\Authorization;

use Muswalo\Surgemusic\Models\User\Login\Login;
use Muswalo\Surgemusic\Utils\Helpers;

/**
 * Autorization controller
 * @property string|null $userId
 * @property string $loginId
 * @property Helpers $helpers
 * 
 */
class Authorization extends Login
{
    protected ?string $userId = null;
    protected string $loginId;
    protected Helpers $helpers;

    /**
     * Authorization constructor.
     *
     * @param Helpers $helpers Instance of Helpers class for utility functions.
     */
    public function __construct(Helpers $helpers)
    {
        $this->helpers = $helpers;
        $this->loginId = $this->helpers->getCookie('login_id');
        $login = $this->find($this->loginId);
        $this->userId = $login ? ($login->attributes['is_valid'] ? $login->attributes['user_id'] : null) : null;
    }

    /**
     * Retrieve the user ID associated with the current login.
     *
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * Checks if the user is authorized.
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->userId !== null;
    }
}
