<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exception\Validation\DtoValidationException;
use Valitron\Validator;

abstract class Dto extends Model
{
    /**
     * @throws DtoValidationException
     */
    protected function validate(array $data, array $rules): void
    {
        $v = new Validator($data);
        $v->rules($rules);

        if (!$v->validate()) {
            throw new DtoValidationException($v->errors(), "Data transfer object failed validation");
        }
    }
}
