<?php

declare(strict_types=1);

namespace App\Dto;

use App\Core\Dto;
use App\Core\Exception\Validation\DtoValidationException;

class LoginDto extends Dto
{
    public ?string $email = null;
    public ?string $password = null;

    /**
     * @throws DtoValidationException
     */
    public function __construct(array $data)
    {
        $this->validate($data, [
            "required" => [
                ["email"],
                ["password"],
            ],
            "email" => [
                ["email"]
            ],
        ]);
        $this->setProperties($data);
    }
}
