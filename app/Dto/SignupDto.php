<?php

declare(strict_types=1);

namespace App\Dto;

use App\Core\Dto;
use App\Core\Exception\Validation\DtoValidationException;

class SignupDto extends Dto
{
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $passwordConfirm = null;

    /**
     * @throws DtoValidationException
     */
    public function __construct(array $data)
    {
        $this->validate($data, [
            "required" => [
                ["username"],
                ["email"],
                ["password"],
                ["passwordConfirm"],
            ],
            "email" => [
                ["email"]
            ],
            "lengthMin" => [
                ["password", 6]
            ],
            "equals" => [
                ["passwordConfirm", "password"],
            ],
        ]);
        $this->setProperties($data);
    }
}
