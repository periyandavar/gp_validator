<?php

namespace Validator\Rules;

use Validator\Field\Field;
use Validator\ValidationConstants;

class ConfirmationValidator extends BaseValidator
{
    private Field $field;

    /**
     * @var string
     */
    private static string $name = ValidationConstants::CONFIRMATION_VALIDATIOR;

    public static function getName(): string
    {
        return self::$name;
    }

    public function __construct(Field $field)
    {
        $this->field = $field;
        $this->messages = [
            'default' => "The value should be equal to {$field->getName()}",
            'unique' => "The value should be equal to {$field->getName()}",
        ];
    }

    public function validate(Field $field): bool
    {
        $result = $this->field->getData() == $field->getData();

        if (! $result) {
            $this->setErrorMessage($field);

            return false;
        }

        return true;
    }
}
