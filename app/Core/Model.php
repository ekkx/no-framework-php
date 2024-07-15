<?php

declare(strict_types=1);

namespace App\Core;

abstract class Model
{
    protected function setProperties(array $data): void
    {
        $object = json_decode(json_encode($data));

        foreach ($object as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
