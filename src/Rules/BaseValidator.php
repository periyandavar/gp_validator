<?php

namespace Validator\Rules;

use Validator\Field\Field;

abstract class BaseValidator implements ValidationRule
{
    protected array $messages = [];

    private $err_msg = '';

    public function getErrorMessage(?string $key = null)
    {
        if (is_null($key)) {
            $key = 'default';
        }

        return $this->messages[$key] ?? $this->messages['default'] ?? 'The value is invalid';
    }

    public function setMessage(string $message, $key = 'default')
    {
        $this->messages[$key] = $message;
    }

    public function getError()
    {
        return $this->frameErrorMessage($this->err_msg ?? $this->getErrorMessage());
    }

    public function setErrorMessage(Field $field, ?string $subRule = null)
    {
        $name = $this->getName();
        $this->err_msg = $field->getRuleMessage($name) ?? $this->getErrorMessage($subRule);
    }

    public function frameErrorMessage(string $message = '')
    {
        $matches = $params = [];

        if (preg_match_all('/\{(.*?)\}/', $message, $matches)) {
            $matches = $matches[1];
        } else {
            return $message;
        }

        $message = str_replace(['{', '}'], '', $message);

        foreach ($matches as $key) {
            if ($this->$key) {
                $params[$key] = $this->$key;
            }
        }

        return strtr($message, $params);
    }
}
