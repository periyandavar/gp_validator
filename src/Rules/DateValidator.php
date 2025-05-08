<?php

namespace Validator\Rules;

use DateTime;
use Validator\Field\Field;
use Validator\ValidationConstants;

class DateValidator extends BaseValidator
{
    public $max;
    public $min;
    private $minDate;
    private $maxDate;
    public $format;

    /**
     * @var string
     */
    private static string $name = ValidationConstants::DATE_VALIDATOR;

    public static function getName(): string
    {
        return self::$name;
    }

    public function __construct($format = 'Y-m-d', $min = null, $max = null)
    {
        $this->format = $format;
        $this->min = $min;
        $this->max = $max;
        $this->maxDate = $this->max ? DateTime::createFromFormat($this->format, $this->max) : null;
        $this->minDate = $this->min ? DateTime::createFromFormat($this->format, $this->min) : null;

        $this->messages = [
            'default' => 'The value should be a date',
            'max' => 'The value should be less than or equal to {max}',
            'min' => 'The value should be greater than or equal to {min}',
            'between' => 'The value should be between {min} and {max}',
            'format' => 'The value should be have the {format} format',
        ];
    }

    public function validate(Field $field): bool
    {
        $date = $field->getData();

        $dateTime = DateTime::createFromFormat($this->format, $date);
        if (! $dateTime || $dateTime->format($this->format) !== $date) {
            $this->setErrorMessage($field, 'format');

            return false;
        }

        if (! empty($this->maxDate) && ! empty($this->minDate)) {
            if ($dateTime < $this->minDate || $dateTime > $this->maxDate) {
                $this->setErrorMessage($field, 'between');

                return false;
            }
        }
        if (! empty($this->maxDate)) {
            if ($dateTime > $this->maxDate) {
                $this->setErrorMessage($field, 'max');

                return false;
            }
        }

        if (! empty($this->minDate)) {
            if ($dateTime < $this->minDate) {
                $this->setErrorMessage($field, 'min');

                return false;
            }
        }

        return true;
    }
}
