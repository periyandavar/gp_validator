<?php

namespace System\Library;

class FormDataValidation
{
    /**
     * Performs the custom validation
     *
     * @param string         $data data to be validate
     * @param ValidationRule $vr   ValidationRule Object defining custom validation
     * @param string         $msg  where the error msg will be stored
     *
     * @return bool
     */
    public function customValidation(
        string $data,
        ValidationRule $vr,
        ?string &$msg = null
    ): bool {
        return $vr->validate($data, $msg);
    }

    /**
     * Check whether the values are in the given set of values
     *
     * @param string $data     data
     * @param mixed  ...$value values set
     *
     * @return bool
     */
    public function valuesInValidation(string $data, ...$value)
    {
        return in_array($data, $value);
    }

    /**
     * Performs mobile number validation
     *
     * @param string $data data
     *
     * @return bool
     */
    public function mobileNumberValidation(string $data): bool
    {
        return preg_match('/^[6789]\d{9}$/', $data);
    }

    /**
     * Performs landline number validation
     *
     * @param string $data data
     *
     * @return bool
     */
    public function landlineValidation(string $data): bool
    {
        return preg_match('/\d{5}([- ]*)\d{6}/', $data);
    }

    /**
     * Performs alpha and space validation
     *
     * @param string $data Data
     *
     * @return bool
     */
    public function alphaSpaceValidation(string $data): bool
    {
        return preg_match('/^[A-Za-z ]*$/', $data);
    }

    /**
     * Performs email validation
     *
     * @param string $data Data
     *
     * @return bool
     */
    public function emailValidation(string $data): bool
    {
        return preg_match(
            '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',
            $data
        );
    }

    /**
     * Isbn Validation
     *
     * @param string $isbn Isbn Number
     *
     * @return bool
     */
    public function isbnValidation(string $isbn): bool
    {
        $n = strlen($isbn);
        if ($n != 10) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            if (!is_numeric($isbn[$i])) {
                return false;
            }
            $digit = (int) ($isbn[$i]);
            $sum += ($digit * (10 - $i));
        }
        $last = $isbn[9];
        if ($last != 'X' && (!is_numeric($last))) {
            return false;
        }
        $sum += (($last == 'X') ? 10 : ((int) $last));

        return ($sum % 11 == 0);
    }

    /**
     * Performs numeric validation and range validation
     *
     * @param string   $data  Data
     * @param int|null $start Starting Index
     * @param int|null $end   Ending Index
     *
     * @return bool
     */
    public function numericValidation(
        string $data,
        ?int $start = null,
        ?int $end = null
    ): bool {
        $flag = is_numeric($data);
        if ($flag) {
            isset($start) && $flag = (int) $data > $start;
            isset($end) && $flag = ($flag && (int) ($data < $end));
        }

        return $flag;
    }

    /**
     * Check whether the data is valid positive number or not
     *
     * @param string $data Data
     *
     * @return bool
     */
    public function positiveNumberValidation(string $data): bool
    {
        return $this->numericValidation($data, -1);
    }

    /**
     * Performs custom reqular expression validation
     *
     * @param string $data       Data
     * @param string $expression Regular expression pattern
     *
     * @return bool
     */
    public function expressValidation(string $data, string $expression): bool
    {
        return preg_match($expression, $data);
    }

    /**
     * Validates the length
     *
     * @param string $data      Data
     * @param int    $minlength Minimum Length
     * @param int    $maxlength Maximum Length
     *
     * @return bool
     */
    public function lengthValidation(
        string $data,
        ?int $minlength,
        ?int $maxlength = null
    ): bool {
        if ($minlength != null) {
            if (strlen($data) < $minlength) {
                return false;
            }
        }
        if ($maxlength != null) {
            if (strlen($data) > $maxlength) {
                return false;
            }
        }

        return true;
    }

    /**
     * Required fields validation
     *
     * @param string $data Data
     *
     * @return bool
     */
    public function required(?string $data): bool
    {
        if ($data == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validates all the passed $fields and returns the validation result
     *
     * @param Fields $fields       Fields object to be validate
     * @param string $invalidField string refernce where the invalid field
     *                             will be stored on this variable
     *
     * @return bool
     */
    public function validate(Fields $fields, &$invalidField = null): bool
    {
        foreach ($fields as $field => $value) {
            $fieldName = $field;
            $data = $value['data'];
            $rules = $value['rule'];
            if ($rules != '' && $rules != null) {
                foreach ((array) $rules as $rule) {
                    if ($rule instanceof ValidationRule) {
                        $newValue = $this->customValidation($data, $rule);
                        if ($newValue === false) {
                            $invalidField = $fieldName;

                            return false;
                        }
                    } else {
                        $params = explode(' ', $rule);
                        $rule = array_shift($params);
                        if (method_exists($this, $rule)) {
                            if (count($params) == 0) {
                                if ($data == null) {
                                    if (in_array('required', (array) $rules)) {
                                        $invalidField = "$fieldName is required";

                                        return false;
                                    } else {
                                        continue;
                                    }
                                } elseif (!$this->$rule($data)) {
                                    $invalidField = "$fieldName";

                                    return false;
                                }
                            } elseif (count($params) != 0) {
                                if ($data == null) {
                                    if (in_array('required', (array) $rules)) {
                                        $invalidField = "$fieldName is required";

                                        return false;
                                    } else {
                                        continue;
                                    }
                                } elseif (!$this->$rule($data, ...$params)) {
                                    $invalidField = $fieldName;

                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }
}
