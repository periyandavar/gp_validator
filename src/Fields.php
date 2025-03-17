<?php

namespace System\Library;

use Iterator;
use Validator\FileUploader;

class Fields implements Iterator
{
    use FileUploader;
    /**
     * List of fields stored in array
     *
     * @var array
     */
    private $_fields;

    /**
     * Instantiate new Fields instance
     *
     * @param array|null $fields Fields
     */
    public function __construct(?array $fields = null)
    {
        if ($fields == null) {
            $this->_fields = [];
        } else {
            foreach ($fields as $field) {
                $this->_fields[$field]['data'] = null;
                $this->_fields[$field]['rule'] = [];
            }
        }
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
            $this->_fields[$field]['data'] = null;
            $this->_fields[$field]['rule'] = [];
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
        $this->_fields = array_values($fields);
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
            if (isset($this->_fields[$key])) {
                $this->_fields[$key]['data'] = $value;
            }
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
        foreach ($this->_fields as $field => $value) {
            $fieldName = $field;
            $data = $value['data'];
            $values[$fieldName] = $data;
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
                    foreach ($values as $value) {
                        $this->_fields[$key]['rule'][] = $value;
                    }
                } else {
                    $this->_fields[$key]['rule'][] = $values;
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
                $this->_fields[$field]['rule'][] = 'required';
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
            if (isset($value['data'])) {
                $fieldsData[$key] = $value['data'];
            }
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
            $this->_fields[$field]['rule'][] = $vr;
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
            $this->_fields[$key]['data'] = $value;
        }
    }

    /**
     * Rewinds the iteration
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->_fields);
    }

    /**
     * Checks this is valid index or not
     *
     * @return bool
     */
    public function valid(): bool
    {
        $flag = key($this->_fields);
        $flag = ($flag !== null);

        return $flag;
    }

    /**
     * Returns the current key
     *
     * @return string
     */
    public function key(): string
    {
        return key($this->_fields);
    }

    /**
     * Returns the current value
     *
     * @return array
     */
    public function current(): array
    {
        return current($this->_fields);
    }

    /**
     * Moves to next index
     *
     * @return void
     */
    public function next()
    {
        next($this->_fields);
    }
}
