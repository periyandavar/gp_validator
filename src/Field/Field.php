<?php

namespace Validator\Field;

use Validator\ValidationEngine;

class Field
{
    protected $name;
    protected $data;
    protected $rules = [];
    protected $messages = [];
    protected ?bool $valid = null;

    protected $errors = [];

    public function __construct($name, $data = null, $rules = [], array $messages = [])
    {
        $this->name = $name;
        $this->data = $data;
        $this->rules = (array) $rules;
        $this->messages = $messages;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     */
    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the value of rules
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Set the value of rules
     */
    public function setRules($rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Get the value of messages
     */
    public function getMessages()
    {
        return $this->messages;
    }

    public function getMessage(string $rule)
    {
        return $this->getMessages()[$rule] ?? null;
    }

    /**
     * Set the value of messages
     */
    public function setMessages($messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    public function addRule($rule)
    {
        $this->rules[] = $rule;
    }

    public function addMessage($message)
    {
        $this->messages[] = $message;
    }

    public function addRules(array $rules)
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    public function validate()
    {
        $ve = new ValidationEngine();

        return $ve->validateField($this);
    }

    /**
     * Get the value of valid
     *
     * @return ?bool
     */
    public function isValid(): ?bool
    {
        return $this->valid;
    }

    /**
     * Set the value of valid
     *
     * @param bool $valid
     *
     * @return self
     */
    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get the value of errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set the value of errors
     */
    public function setErrors($errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function addError($error)
    {
        $this->errors[] = $error;

        return $this;
    }

    public function getError()
    {
        return reset($this->errors);
    }
}
