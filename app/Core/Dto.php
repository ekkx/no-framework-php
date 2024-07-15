<?php

declare(strict_types=1);

namespace App\Core;

use Valitron\Validator;

abstract class Dto extends Model
{
    private array $validationErrors = [];
    private bool $validationFailed = false;

    protected function validate(array $data, array $rules): void
    {
        $v = new Validator($data);
        $v->rules($rules);

        if (!$v->validate()) {
            $this->validationFailed = true;
            $this->validationErrors = $v->errors();
        }
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function isValidationFailed(): bool
    {
        return $this->validationFailed;
    }
}
