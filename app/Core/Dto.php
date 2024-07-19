<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exception\ValidationException;
use Valitron\Validator;

abstract class Dto extends Model
{
    /**
     * @throws ValidationException
     */
    protected function validate(array $data, array $rules): void
    {
        $v = new Validator($data);
        $v->rules($rules);

        if (!$v->validate()) {
            throw new ValidationException($v->errors(), "Data transfer object failed validation");
        }
    }
}
