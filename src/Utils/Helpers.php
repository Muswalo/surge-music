<?php

namespace Muswalo\Surgemusic\Utils;

class Helpers
{
    /**
     * Sends a formatted response to the client.
     *
     * @param string $status       The status message (success, error, etc.)
     * @param int $status_code     The HTTP status code (200, 400, etc.)
     * @param string $message      The message to send back in the response
     * @param array $data          Any additional data to include in the response
     * @return void
     */
    public function send_response($status, $status_code, $message, $data = [])
    {
        http_response_code($status_code);
        echo json_encode(array_merge(['status' => $status, 'message' => $message], $data));
        exit();
    }

    /**
     * Generates a unique UUID (Universally Unique Identifier).
     *
     * @return string The generated UUID
     */
    public function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Sets a cookie with a specific value and optional attributes.
     *
     * @param string $name         The name of the cookie
     * @param string $value        The value of the cookie
     * @param int $expiry          The expiry time of the cookie in seconds (default is 1 hour)
     * @param string $path         The path where the cookie is available (default is '/')
     * @param string $domain       The domain where the cookie is available (default is empty)
     * @param bool $secure         Whether the cookie should be sent over secure HTTPS connections (default is false)
     * @param bool $httponly       Whether the cookie should be accessible only via HTTP (default is true)
     * @return void
     */
    public function setCookie($name, $value, $expiry = 3600, $path = '/', $domain = '', $secure = false, $httponly = true)
    {
        setcookie($name, $value, time() + $expiry, $path, $domain, $secure, $httponly);
    }

    /**
     * Checks if a cookie exists.
     *
     * @param string $name The name of the cookie to check
     * @return bool True if the cookie exists, false otherwise
     */
    public function checkCookieExists($name)
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * Destroys a cookie by setting its expiration time to the past.
     *
     * @param string $name   The name of the cookie to destroy
     * @param string $path   The path where the cookie is available (default is '/')
     * @param string $domain The domain where the cookie is available (default is empty)
     * @return void
     */
    public function destroyCookie($name, $path = '/', $domain = '')
    {
        if (isset($_COOKIE[$name])) {
            // Set the cookie's expiration time to the past, effectively deleting it
            setcookie($name, '', time() - 3600, $path, $domain);
            unset($_COOKIE[$name]);
        }
    }

    /**
     * Retrieves the value of a cookie, if it exists.
     *
     * @param string $name The name of the cookie
     * @return string|null The cookie value, or null if the cookie does not exist
     */
    public function getCookie($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    /**
     * Clears all cookies by setting their expiration time to the past.
     *
     * @param string $path   The path where the cookies are available (default is '/')
     * @param string $domain The domain where the cookies are available (default is empty)
     * @return void
     */
    public function clearAllCookies($path = '/', $domain = '')
    {
        foreach ($_COOKIE as $name => $value) {
            setcookie($name, '', time() - 3600, $path, $domain);
            unset($_COOKIE[$name]);
        }
    }

    /**
     * Validates an email address format.
     *
     * @param string $email The email address to validate
     * @return bool True if the email is valid, false otherwise
     */
    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Hashes a string using the bcrypt algorithm.
     *
     * @param string $input The string to hash
     * @return string The hashed string
     */
    public function hashString($input)
    {
        return password_hash($input, PASSWORD_DEFAULT);
    }

    /**
     * Verifies a hashed string against an input.
     *
     * @param string $input The input string to verify
     * @param string $hash  The hashed string to verify against
     * @return bool True if the input matches the hash, false otherwise
     */
    public function verifyHash($input, $hash)
    {
        return password_verify($input, $hash);
    }

    /**
     * Sanitizes a string to prevent XSS (Cross-Site Scripting) attacks.
     *
     * @param string $input The string to sanitize
     * @return string The sanitized string
     */
    public function sanitizeString($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}
