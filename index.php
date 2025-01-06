<?php

require_once __DIR__ . '/vendor/autoload.php';

use Muswalo\Surgemusic\Controllers\Auth\Authentication\Authentication;
use Muswalo\Surgemusic\Controllers\LoginController;

header("content-type: application/json");
$loginInstance = new LoginController();
$auth = new Authentication($loginInstance);
// $data = [
//     'first_name' => "Emmanuel", 
//     'last_name' => "Muswalo", 
//     'username' => "emmanuelmuswalo", 
//     'email' => "example@mail.com", 
//     'phone' => "123456789", 
//     'image' => "https://via.placeholder.com/150", 
//     'password' => password_hash("123456", PASSWORD_DEFAULT), 
//     'created_at', 
//     'updated_at', 
//     'deleted_at', 
//     'verified_at', 
//     'is_verified', 
//     'is_agreed_to_terms', 
//     'google_id', 
//     'provider' => "local"
// ];
$response = $auth->loginUser("example@mail.com", "123456");

print_r($response->toJson());