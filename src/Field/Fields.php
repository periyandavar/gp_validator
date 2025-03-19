<?php

namespace Validator\Field;

use ArrayIterator;
use IteratorAggregate;
use Validator\FileUploader;
use Validator\Rules\ValidationRule;

class Fields implements IteratorAggregate
{
    use FileUploader;
    /**
     * List of fields stored in array
     *
     * @var Field[]
     */
    private $_fields;

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

    public function addField($field)
    {
        if ($field instanceof Field) {
            $this->_fields[$field->getName()] = $field;

            return;
        }

        $this->_fields[$field] = new Field($field);
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
            unset($this->$fields[$field]);
        }
        // $this->_fields = array_values($fields);
    }

    /**
     * Populates the values to fields
     *
     * @param array $values Values for the fields
     *
     * @return void
     */
    public function addValues(array $values)
    {
        foreach ($values as $key => $value) {
            $this->_fields[$key]->setData($value);
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
        foreach ($this->_fields as $value) {
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
            if (isset($this->_fields[$key])) {
                if (is_array($values)) {
                    $this->_fields[$key]->addRules($values);
                } else {
                    $this->_fields[$key]->addRule($values);
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
            if (isset($this->_fields[$field])) {
                $this->_fields[$field]->addRule('required');
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
        if (array_key_exists($oldName, $this->_fields)) {
            $this->_fields[$oldName]->setName($newName);
            $this->_fields[$newName] = $this->_fields[$oldName];
            unset($this->_fields[$oldName]);
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
        foreach ($this->_fields as $key => $value) {
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
        if (isset($this->_fields[$field])) {
            $this->_fields[$field]->addRule($vr);
        }
    }

    /**
     * Change the data value for the fields
     *
     * @param string $key   field name
     * @param string $value filed value
     *
     * @return void
     */
    public function setData(string $key, string $value)
    {
        if (isset($this->_fields[$key])) {
            $this->_fields[$key]->setData($value);
        }
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->_fields);
    }

    public function validate(): bool
    {
        $flag = true;
        foreach ($this->_fields as $field) {
            $flag = $field->validate() && $flag;
        }

        return $flag;
    }
}
