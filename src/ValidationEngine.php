<?php

namespace Validator;

use Validator\Field\Field;
use Validator\Field\Fields;
use Validator\Rules\ValidationRule;

class ValidationEngine
{
    private function updateFieldStatus(bool $result, Field $field, string $message)
    {
        $field->setValid($field->isValid() === null ? $result : $field->isValid() && $result);

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? [];
        $rule = preg_replace('/Validation$/', '', $backtrace['function'] ?? $backtrace['class'] ?? '');
        $rule = $backtrace['function'] === 'handleValidationRule' ? preg_replace('/Validation$/', '', $backtrace['class'] ?? '') : $rule;

        $error = $field->getMessage($rule) ?? $message;

        if (! $result) {
            $field->addError($error);
        }

        return $result;
    }

    /**
     * Check whether the values are in the given set of values
     *
     * @param Field $field    Field
     * @param mixed ...$value values set
     *
     * @return bool
     */
    public function valuesInValidation(Field $field, $value)
    {
        $data = $field->getData();

        return $this->updateFieldStatus(in_array($data, $value), $field, "{$field->getName()} should have only these possible values [" . implode($value) . ']');
    }

    /**
     * Performs mobile number validation
     *
     * @param Field $field
     *
     * @return bool
     */
    public function mobileNumberValidation(Field $field): bool
    {
        $data = $field->getData();

        return $this->updateFieldStatus(preg_match('/^[6789]\d{9}$/', $data), $field, "{$field->getName()} should be a valid Mobile Number");
    }

    /**
     * Performs landline number validation
     *
     * @param Field $field
     *
     * @return bool
     */
    public function landlineValidation(Field $field): bool
    {
        $data = $field->getData();

        return $this->updateFieldStatus(preg_match('/\d{5}([- ]*)\d{6}/', $data), $field, "{$field->getName()} should be the valid Landline number");
    }

    /**
     * Performs alpha and space validation
     *
     * @param Field $field
     *
     * @return bool
     */
    public function alphaSpaceValidation(Field $field): bool
    {
        $data = $field->getData();

        return $this->updateFieldStatus(preg_match('/^[A-Za-z ]*$/', $data), $field, "{$field->getName()} should be alphabetic with spaces");
    }

    /**
     * Performs email validation
     *
     * @param Field $field
     *
     * @return bool
     */
    public function emailValidation(Field $field): bool
    {
        $data = $field->getData();

        $result = preg_match(
            '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',
            $data
        );

        return $this->updateFieldStatus($result, $field, "{$field->getName()} should be a valid email id");
    }

    /**
     * Isbn Validation
     *
     * @param Field $field
     *
     * @return bool
     */
    public function isbnValidation(Field $field): bool
    {
        $isbn = $field->getData();
        $n = strlen($isbn);
        if ($n != 10) {
            return $this->updateFieldStatus(false, $field, "{$field->getName()} shoule be the valid ISBN");
        }
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            if (! is_numeric($isbn[$i])) {
                return $this->updateFieldStatus(false, $field, "{$field->getName()} shoule be the valid ISBN");
            }
            $digit = (int) ($isbn[$i]);
            $sum += ($digit * (10 - $i));
        }
        $last = $isbn[9];
        if ($last != 'X' && (! is_numeric($last))) {
            return $this->updateFieldStatus(false, $field, "{$field->getName()} shoule be the valid ISBN");
        }
        $sum += (($last == 'X') ? 10 : ((int) $last));

        $result = ($sum % 11 == 0);

        return $this->updateFieldStatus($result, $field, "{$field->getName()} shoule be the valid ISBN");
    }

    /**
     * Performs numeric validation and range validation
     *
     * @param Field $field  Field
     * @param array $params
     *
     * @return bool
     */
    public function numericValidation(
        Field $field,
        array $params
    ): bool {
        $data = $field->getData();
        $flag = is_numeric($data);
        $name = $field->getName();

        if (! $flag) {
            return $this->updateFieldStatus($flag, $field, "{$name} should be valid number");
        }
        $start = $params['min'] ?? $params[0] ?? null;
        if (isset($start) && (int) $data < $start) {
            return $this->updateFieldStatus(false, $field, "{$name} should have the value minimum of $start");
        }

        $end = $params['max'] ?? $params[1] ?? null;
        if (isset($end) && (int) $data > $end) {
            return $this->updateFieldStatus(false, $field, "{$name} should have the value maximum of $end");
        }

        return $this->updateFieldStatus(true, $field, '');
    }

    /**
     * Check whether the data is valid positive number or not
     *
     * @param Field $field
     *
     * @return bool
     */
    public function positiveNumberValidation(Field $field): bool
    {
        $data = $field->getData();
        $result = is_numeric($data) && (int) $data > 0;

        return $this->updateFieldStatus($result, $field, "{$field->getName()} should be a positive number");
    }

    /**
     * Performs custom reqular expression validation
     *
     * @param Field  $field
     * @param string $expression Regular expression pattern
     *
     * @return bool
     */
    public function regexValidation(Field $field, string $expression): bool
    {
        $result = preg_match($expression, $field->getData());

        return $this->updateFieldStatus($result, $field, "{$field->getName()} should match the regex format : {$expression}");
    }

    /**
     * Validates the length
     *
     * @param Field $field
     * @param array $params
     *
     * @return bool
     */
    public function lengthValidation(
        Field $field,
        array $params = []
    ): bool {
        $data = $field->getData();
        $name = $field->getName();
        $minlength = $params['min'] ?? $params[0] ?? null;
        if ($minlength != null) {
            if (strlen($data) < $minlength) {
                return $this->updateFieldStatus(false, $field, "{$name} should have atleast {$minlength} characters");
            }
        }
        $maxlength = $params['max'] ?? $params[1] ?? null;
        if ($maxlength != null) {
            if (strlen($data) > $maxlength) {
                return $this->updateFieldStatus(false, $field, "{$name} should have atmost {$maxlength} characters");
            }
        }

        return $this->updateFieldStatus(true, $field, '');
    }

    /**
     * Required fields validation
     *
     * @param Field $field
     *
     * @return bool
     */
    public function requiredValidation(Field $field): bool
    {
        return $this->updateFieldStatus(! $field->getData() == null, $field, "{$field->getName()} should have value");
    }

    /**
     * Validates all the passed $fields and returns the validation result
     *
     * @param Fields $fields              Fields object to be validate
     * @param bool   $pass_on_fail        whether to pass on fail or not
     * @param array  $invalidFieldDetails string refernce where the invalid field
     *                                    will be stored on this variable
     *
     * @return bool
     */
    public function validate(Fields $fields, $pass_on_fail = true, &$invalidFieldDetails = []): bool
    {
        $flag = true;
        foreach ($fields as $field) {
            $result = $this->validateField($field, $pass_on_fail);
            $flag = $flag && $result;
            if (! $field->isValid()) {
                $invalidFieldDetails[] = [$field->getName() => $field->getErrors()];
            }
        }

        return $flag;
    }

    private function executeRule(Field $field, $rule, $pass_on_fail = true, mixed $params = [])
    {
        if ($pass_on_fail && $field->isValid() === false) {
            return;
        }

        if (is_string($rule) && class_exists($rule)) {
            $ruleClass = new $rule();
            if ($ruleClass instanceof ValidationRule) {
                return $this->handleValidationRule($field, $ruleClass, $params);
            }
        }

        if ($rule instanceof ValidationRule) {
            return $this->handleValidationRule($field, $rule, $params);
        }

        if (is_array($rule)) {
            $params = $rule;
            $ruleName = array_shift($params);
            $params = reset($params);

            return $this->executeRule($field, $ruleName, $pass_on_fail, $params);
        }

        $params1 = explode(' ', $rule);
        $rule = array_shift($params1);
        if (is_array($params)) {
            $params = array_merge($params1, $params);
        } else {
            $params1[] = array_merge($params1, [$params]);
        }
        $rule = $rule = preg_replace('/Validation$/', '', $rule) . 'Validation';
        if (method_exists($this, $rule)) {
            return call_user_func([$this, $rule], $field, $params);
        }

        return false;
    }

    public function handleValidationRule(Field $field, ValidationRule $rule, mixed $params)
    {
        $data = $field->getData();
        if (is_array($params)) {
            $data = array_merge([$data], $params);
        }
        if (! is_array($data)) {
            $data = [$data, $params];
        }
        $message = '';

        $result = $rule->validate($data, $message);

        if (! $result) {
            $field->addMessage($message);
        }

        return $this->updateFieldStatus($result, $field, $message);
    }

    public function validateField(Field $field, $pass_on_fail = true): bool
    {
        $flag = true;
        $rules = $field->getRules();
        foreach ($rules as $rule) {
            $result = $this->executeRule($field, $rule);
            $flag = $flag && $result;
            if ($pass_on_fail && ! $flag) {
                return false;
            }
        }
        if ($flag === $field->isValid()) {
            return $flag;
        }

        return false;
    }
}
