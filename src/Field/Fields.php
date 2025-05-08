<?php

namespace Validator\Field;

use ArrayIterator;
use IteratorAggregate;
use Validator\Rules\ValidationRule;

class Fields implements IteratorAggregate
{
    /**
     * List of fields stored in array
     *
     * @var Field[]
     */
    private $fields = [];

    /**
     * Instantiate new Fields instance
     *
     * @param array $fields Fields
     */
    public function __construct(array $fields = [])
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    /**
     * Add a field
     *
     * @param Field|array|string $field
     *
     * Support values for tha param
     * array as ['name' => $name, 'data' => null, 'rules' => [], 'messages' => []]
     * name is required key in the array
     * else only name
     *
     * @return void
     */
    public function addField($field)
    {
        if ($field instanceof Field) {
            $this->fields[$field->getName()] = $field;

            return;
        }

        if (is_array($field) && isset($field['name'])) {
            $name = $field['name'];
            $data = $field['data'] ?? null;
            $rules = $field['rules'] ?? [];
            $messages = $field['messages'] ?? [];
            $this->fields[$name] = new Field($name, $data, $rules, $messages);

            return;
        }

        $this->fields[$field] = new Field($field);
    }

    /**
     * Adds the new set of fields
     *
     * @param mixed ...$fields new fields set
     *
     * @return void
     */
    public function addFields(...$fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    /**
     * Removes the set of passed fields(strings) to $fields
     *
     * @param string ...$fields set of fields to be removed
     *
     * @return void
     */
    public function removeFields(...$fields)
    {
        foreach ($fields as $field) {
            unset($this->fields[$field]);
        }
    }

    /**
     * Returns the fields with their values as array
     *
     * @return array
     */
    public function getValues()
    {
        $values = [];
        foreach ($this->fields as $value) {
            $values[$value->getName()] = $value->getData();
        }

        return $values;
    }

    /**
     * Populates the rules to the fields
     *
     * @param array $fieldsRules as (fields=>rules)
     *
     * @return void
     */
    public function addRule(array $fieldsRules)
    {
        foreach ($fieldsRules as $key => $values) {
            if (isset($this->fields[$key])) {
                if (is_array($values)) {
                    $this->fields[$key]->addRules($values);
                } else {
                    $this->fields[$key]->addRule($values);
                }
            }
        }
    }

    /**
     * Sets the required fields
     *
     * @param string ...$fields fields to be set required
     *
     * @return void
     */
    public function setRequiredFields(...$fields)
    {
        foreach ($fields as $field) {
            if (isset($this->fields[$field])) {
                $this->fields[$field]->addRule('required');
            }
        }
    }

    /**
     * Renames the field
     *
     * @param string $oldName old name
     * @param string $newName new name
     *
     * @return void
     */
    public function renameFieldName(string $oldName, string $newName)
    {
        if (array_key_exists($oldName, $this->fields)) {
            $this->fields[$oldName]->setName($newName);
            $this->fields[$newName] = $this->fields[$oldName];
            unset($this->fields[$oldName]);
        }
    }


    /**
     * Returns fields data values as association array
     *
     * @return array
     */
    public function getData(): array
    {
        $fieldsData = [];
        foreach ($this->fields as $key => $value) {
            $fieldsData[$key] = $value->getData();
        }

        return $fieldsData;
    }

    /**
     * Adds  the custom rule to the fields
     *
     * @param string         $field fieldname
     * @param ValidationRule $vr    ValidationRule Object
     *
     * @return void
     */
    public function addCustomeRule(string $field, ValidationRule $vr)
    {
        if (isset($this->fields[$field])) {
            $this->fields[$field]->addRule($vr);
        }
    }

    /**
     * Change the data value for the fields
     *
     * @param string $key   field name
     * @param mixed  $value filed value
     *
     * @return void
     */
    public function setData(string $key, mixed $value)
    {
        if (isset($this->fields[$key])) {
            $this->fields[$key]->setData($value);
        }
    }

    /**
     * Set the values
     *
     * @param array $data
     *
     * @return void
     */
    public function setValues(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }
    }

    /**
     * Return the iterator object.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->fields);
    }

    /**
     * Validates the field.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $flag = true;
        foreach ($this->fields as $field) {
            $flag = $field->validate() && $flag;
        }

        return $flag;
    }

    /**
     * Get the error messages.
     *
     * @return array
     */
    public function getErrors(): array
    {
        $errors = [];
        foreach ($this->fields as $field) {
            $errors = array_merge($errors, $field->getErrors());
        }

        return $errors;
    }

    /**
     * Return a single error message.
     *
     * @return string
     */
    public function getError(): string
    {
        $errors = $this->getErrors();

        return reset($errors);
    }

    public function getWarnings()
    {
        $warnings = [];
        foreach ($this as $field) {
            $warnings = array_merge($warnings, $field->getWarnings());
        }

        return $warnings;
    }

    public function getWarning()
    {
        $warnings = $this->getWarnings();

        return reset($warnings);
    }

    public function getInvalidFields()
    {
        return array_filter($this->_fields, function(Field $field) {
            return ! $field->isValid();
        });
    }

    public function getValidFields()
    {
        return array_filter($this->_fields, function(Field $field) {
            return $field->isValid() === true;
        });
    }

    public function getFields()
    {
        return array_keys($this->_fields);
    }
}
