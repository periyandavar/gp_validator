<?php

namespace Validator\Rules;

use Validator\Field\Field;
use Validator\ValidationConstants;

class LengthValidator extends BaseValidator
{
    public $max;
    public $min;
    public $exact;

    /**
     * @var string
     */
    private static string $name = ValidationConstants::LENGTH_VALIDATOR;

    public static function getName(): string
    {
        return self::$name;
    }

    public function __construct($min = null, $max = null, $exact = null)
    {
        $this->max = $max;
        $this->min = $min;
        $this->exact = $exact;
        $this->messages = [
            'default' => 'The value has invalid length',
            'max' => 'The value should has less than or equal to {max} characters',
            'min' => 'The value should has greater than or equal to {min} characters',
            'between' => 'The value should has between {min} and {max} characters',
            'exact' => 'The value should has exactly {max} characters',
        ];
    }

    public function validate(Field $field): bool
    {
        $data = $field->getData();
        $len = strlen($data);

        if (! empty($this->exact)) {
            if ($len != $this->exact) {
                $this->setErrorMessage($field, 'exact');

                return false;
            }
        }
        if (! empty($this->max) && ! empty($this->min)) {
            if ($len < $this->min || $len > $this->max) {
                $this->setErrorMessage($field, 'between');

                return false;
            }
        }

        if (! empty($this->max)) {
            if ($len > $this->max) {
                $this->setErrorMessage($field, 'max');

                return false;
            }
        }

        if (! empty($this->min)) {
            if ($len < $this->min) {
                $this->setErrorMessage($field, 'min');

                return false;
            }
        }

        return true;
    }
}
