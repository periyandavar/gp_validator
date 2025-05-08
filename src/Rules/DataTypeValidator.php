<?php

namespace Validator\Rules;

use Validator\Field\Field;
use Validator\ValidationConstants;

class DataTypeValidator extends BaseValidator
{
    /**
     * @var string
     */
    public string $type;

    private static string $name = ValidationConstants::DATA_TYPE_VALIDATOR;

    public static function getName(): string
    {
        return self::$name;
    }

    /**
     * DataTypeValidationRule constructor.
     *
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
        $this->messages = [
            'string' => 'The value should be a string',
            'integer' => 'The value should be an integer',
            'float' => 'The value should be a float',
            'boolean' => 'The value should be a boolean',
            'array' => 'The value should be an array',
            'null' => 'The value should be null',
            'default' => "The value should be {$this->type} data type",
        ];
    }

    public function validate(Field $field): bool
    {
        $result = $this->validateDataType($field->getData(), $this->type);
        if (! $result) {
            $this->setErrorMessage($field, $this->type);

            return false;
        }

        return true;
    }

    public function validateDataType($value, $type)
    {
        switch ($type) {
            case 'string':
                return is_string($value);
            case 'integer':
                return is_int($value);
            case 'float':
                return is_float($value);
            case 'boolean':
                return is_bool($value);
            case 'array':
                return is_array($value);
            case 'null':
                return is_null($value);
            default:
                return false;
        }
    }

    public function getDefaultMessage()
    {
        return $this->messages[$this->type] ?? $this->messages['default'];
    }
}
