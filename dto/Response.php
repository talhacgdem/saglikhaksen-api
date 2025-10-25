<?php

namespace dto;

use JsonSerializable;

class Response implements JsonSerializable
{
    public bool $success;
    public mixed $data;
    public ?Pageable $meta;
    public ?array $error;

    public function __construct(bool $success, mixed $data = null, Pageable $meta = null, ?array $error = null)
    {
        $this->success = $success;
        $this->data = $data;
        $this->meta = $meta;
        $this->error = $error;
    }

    public static function success(mixed $data, Pageable $meta = null): self
    {
        return new self(true, $data, $meta, null);
    }

    public static function error(string $message, int $code = 400): self
    {
        http_response_code($code);
        return new self(false, null, null, [
            'message' => $message,
            'code' => $code
        ]);
    }

    public function jsonSerialize(): array
    {
        return [
            'success' => $this->success,
            'data' => $this->data,
            'meta' => $this->meta,
            'error' => $this->error,
        ];
    }
}
