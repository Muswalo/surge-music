<?php

namespace Muswalo\Surgemusic\Controllers;

use Muswalo\Surgemusic\Models\User\Login\Login;
class LoginController extends Login
{
    public function login(array $data): self
    {
        return $this->create($data);
    }

    public function logout(): bool
    {
        $helpers = new \Muswalo\Surgemusic\Utils\Helpers();
        $helpers->destroyCookie('login_id');
        return true;
    }

    public function inValidateLogin (int $id): bool {
        return true;
    }
}
