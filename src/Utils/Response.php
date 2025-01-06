<?php

namespace Muswalo\Surgemusic\Utils;

/**
 * Class Response
 * 
 * Represents a standardized structure for API responses.
 */
class Response
{
    /**
     * @var bool $success Indicates whether the operation was successful.
     */
    public bool $success;

    /**
     * @var string|null $message Optional message describing the result.
     */
    public ?string $message;

    /**
     * @var array|null $errors Optional array of errors, if any.
     */
    public ?array $errors;

    /**
     * @var mixed $data The payload or data associated with the response.
     */
    public mixed $data;

    /**
     * Response constructor.
     *
     * @param bool $success Indicates whether the operation was successful.
     * @param string|null $message An optional message describing the result.
     * @param array|null $errors An optional array of errors, if any.
     * @param mixed $data The data or payload associated with the response.
     */
    public function __construct(bool $success, ?string $message = null, ?array $errors = null, mixed $data = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->errors = $errors;
        $this->data = $data;
    }

    /**
     * Creates a successful response.
     *
     * @param mixed $data Optional data to include in the response.
     * @param string|null $message Optional success message.
     * @return self A Response instance representing success.
     */
    public static function success(mixed $data = null, string $message = null): self
    {
        return new self(true, $message, null, $data);
    }

    /**
     * Creates an error response.
     *
     * @param string|null $message Optional error message.
     * @param array $errors Optional array of error details.
     * @param mixed $data Optional data to include in the response.
     * @return self A Response instance representing an error.
     */
    public static function error(string $message = null, array $errors = [], mixed $data = null): self
    {
        return new self(false, $message, $errors, $data);
    }

    /**
     * Converts the response object to a JSON string.
     *
     * @return string The JSON representation of the response.
     */
    public function toJson(): string
    {
        return json_encode($this);
    }
}
