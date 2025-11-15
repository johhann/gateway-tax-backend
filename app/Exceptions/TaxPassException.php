<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TaxPassException extends Exception
{
    protected mixed $payload;

    public function __construct(string $message = '', int $code = 0, $payload = null, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->payload = $payload;
    }

    /**
     * @return mixed|null
     */
    public function getPayload(): mixed
    {
        return $this->payload;
    }
}
