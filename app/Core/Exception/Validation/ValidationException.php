<?php

declare(strict_types=1);

namespace App\Core\Exception\Validation;

use App\Core\Exception\BaseException;
use Throwable;

class ValidationException extends BaseException
{
    protected array $errors;

    public function __construct(array $errors, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
