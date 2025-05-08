<?php

namespace Validator\Rules;

use Validator\Field\Field;

interface ValidationRule
{
    /**
     * Validation Rule
     *
     * @param Field $field Field to be validated
     *
     * @return bool
     */
    public function validate(Field $field): bool;

    public static function getName(): string;
}
