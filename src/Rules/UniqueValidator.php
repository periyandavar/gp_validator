<?php

namespace Validator\Rules;

use Validator\Field\Field;
use Validator\ValidationConstants;

class UniqueValidator extends BaseValidator
{
    public $db;
    public $handler;

    public $params;

    /**
     * @var string
     */
    private static string $name = ValidationConstants::UNIQUE_VALIEATOR;

    public static function getName(): string
    {
        return self::$name;
    }

    public function __construct($db, $handler, $params = [])
    {
        $this->db = $db;
        $this->handler = $handler;
        $this->params = $params;
        $this->messages = [
            'default' => 'The value should be unique',
            'unique' => 'The value should be unique',
        ];
    }

    public function validate(Field $field): bool
    {
        $data = $field->getData();
        $handler = $this->handler;
        $result = call_user_func([$this->db, $handler], $this->params, $data);

        if (! $result) {
            $this->setErrorMessage($field);

            return false;
        }

        return true;
    }
}
