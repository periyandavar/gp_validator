<?php

namespace Validator\Rules;

use Validator\Field\Field;
use Validator\ValidationConstants;

class NumericValidator extends BaseValidator
{
    public $max;
    public $min;

    /**
     * @var string
     */
    private static string $name = ValidationConstants::NUMERIC_VALIDATOR;

    public static function getName(): string
    {
        return self::$name;
    }

    public function __construct($min = null, $max = null)
    {
        $this->max = $max;
        $this->min = $min;
        $this->messages = [
            'default' => 'The value should be a numeric value',
            'max' => 'The value should be less than or equal to {max}',
            'min' => 'The value should be greater than or equal to {min}',
            'between' => 'The value should be between {min} and {max}',
        ];
    }

    public function validate(Field $field): bool
    {
        $data = $field->getData();
        $result = is_numeric($data);
        if (! $result) {
            $this->setErrorMessage($field);

            return false;
        }
        if ((! empty($this->max) && ! empty($this->min)) && ($data < $this->min || $data > $this->max)) {
            $this->setErrorMessage($field, 'between');

            return false;
        }
        if ((! empty($this->max)) && ($data > $this->max)) {
            $this->setErrorMessage($field, 'max');

            return false;
        }

        if ((! empty($this->min)) && ($data < $this->min)) {
            $this->setErrorMessage($field, 'min');

            return false;
        }

        return true;
    }
}
