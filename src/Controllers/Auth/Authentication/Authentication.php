<?php

namespace Muswalo\Surgemusic\Controllers\Auth\Authentication;

use Muswalo\Surgemusic\Models\User\User;
use Muswalo\Surgemusic\Utils\Response;
use Muswalo\Surgemusic\Controllers\LoginController;
use Muswalo\Surgemusic\Utils\Helpers;

/**
 * Authentication controller
 * @property User $user
 */

class Authentication extends User
{
    protected LoginController $login;
    protected Helpers $helpers;

    public function __construct(LoginController $login)
    {
        parent::__construct();
        $this->login = $login;
        $this->helpers = new Helpers();
    }

    public  function createUser(array $data): Response
    {
        try {
            $user = $this->create($data);
            $loginData = $this->generateLoginData($user->attributes['id']);
            $login = $this->login->create($loginData);
            return Response::success($login->toArray(), "User created successfully");
        } catch (\Throwable $th) {
            var_dump($th);
            return Response::error("Failed to create user", ['error' => $th->getMessage()]);
        }
    }

    public function loginUser(string $email, string $password): Response
    {
        $user = $this->findBy("email", $email);
        if (!$user) {
            return Response::error("Invalid email or password");
        }

        $hash = $user->attributes["password"];

        var_dump($user);
        if (!$this->helpers->verifyHash($password, $hash)) {
            return Response::error("Invalid email or password");
        }

        $loginData = $this->generateLoginData($user->attributes['id']);
        $login = $this->login->create($loginData);
        return Response::success($login->toArray(), "User logged in successfully.");
    }

    /**
     * Generate login data array
     */
    private function generateLoginData(int $userId): array
    {
        return [
            'user_id' => $userId,
            'ip_address' => $_SERVER["REMOTE_ADDR"],
            'user_agent' => $_SERVER["HTTP_USER_AGENT"],
            'location' => "location",
            'is_valid' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+6 months')),
            'deleted_at' => null
        ];
    }
}
