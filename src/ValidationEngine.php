<?php

namespace Validator;

use Validator\Field\Field;
use Validator\Field\Fields;
use Validator\Rules\BaseValidator;
use Validator\Rules\ValidationRule;

class ValidationEngine
{
    private function updateFieldStatus(bool $result, Field $field, string $message)
    {
        $field->setValid($field->isValid() === null ? $result : $field->isValid() && $result);

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? [];
        $rule = preg_replace('/Validation$/', '', $backtrace['function'] ?? $backtrace['class'] ?? '');
        $rule = $backtrace['function'] === 'handleVrRule' ? preg_replace('/Validation$/', '', $backtrace['class'] ?? '') : $rule;

        if (! $result) {
            $error = $field->getMessage($rule) ?? $message;
            $field->addError($error);
        }

        return $result;
    }

    public function urlValidation(Field $field): bool
    {
        $data = $field->getData();
        $result = filter_var($data, FILTER_VALIDATE_URL);

        return $this->updateFieldStatus($result, $field, "{$field->getName()} should be a valid URL");
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
        $value = is_array($value) ? $value : [$value];

        return $this->updateFieldStatus(in_array($data, $value), $field, "{$field->getName()} should have only these possible values [" . implode(', ', $value) . ']');
    }

    /**
     * Check whether the values are in the given set of values
     *
     * @param Field $field    Field
     * @param mixed ...$value values set
     *
     * @return bool
     */
    public function valuesNotInValidation(Field $field, $value)
    {
        $data = $field->getData();

        $value = is_array($value) ? $value : [$value];

        return $this->updateFieldStatus(! in_array($data, $value), $field, "{$field->getName()} should not have only these values [" . implode(', ', $value) . ']');
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
     * Performs alpha and space validation
     *
     * @param Field $field
     *
     * @return bool
     */
    public function alphaNumericValidation(Field $field): bool
    {
        $data = $field->getData();

        return $this->updateFieldStatus(preg_match('/^[a-zA-Z0-9]+$/', $data), $field, "{$field->getName()} should be alphanumeric");
    }

    /**
     * Performs alpha validation
     *
     * @param Field $field
     *
     * @return bool
     */
    public function alphaValidation(Field $field): bool
    {
        $data = $field->getData();

        return $this->updateFieldStatus(preg_match('/^[A-Za-z]*$/', $data), $field, "{$field->getName()} should be alphabetic");
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
    public function isbnValidation(Field $field)
    {
        // Remove any non-digit characters (except for 'X' in ISBN-10)
        $isbn = $field->getData();
        $isbn = preg_replace('/[^0-9X]/i', '', $isbn);

        // Check for ISBN-10
        if (strlen($isbn) === 10) {
            return $this->isbn10Validation($field);
        }

        // Check for ISBN-13
        if (strlen($isbn) === 13) {
            return $this->isbn13Validation($field);
        }

        return $this->updateFieldStatus(false, $field, "{$field->getName()} shoule be the valid ISBN"); // Invalid length
    }

    /**
     * Isbn10 Validation
     *
     * @param Field $field
     *
     * @return bool
     */
    public function isbn10Validation(Field $field)
    {
        $isbn = $field->getData();
        $isbn = preg_replace('/[^0-9X]/i', '', $isbn);

        if (strlen($isbn) !== 10) {
            return $this->updateFieldStatus(false, $field, "{$field->getName()} shoule be the valid ISBN"); // Invalid length
        }

        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            if (! is_numeric($isbn[$i])) {
                return $this->updateFieldStatus(false, $field, "{$field->getName()} shoule be the valid ISBN"); // Invalid character
            }
            $sum += ($i + 1) * (int) $isbn[$i];
        }

        // Check the last character
        $lastChar = strtoupper($isbn[9]);
        if ($lastChar === 'X') {
            $sum += 10 * 10; // 'X' counts as 10
        } else {
            $sum += 10 * (int) $lastChar;
        }

        return $this->updateFieldStatus($sum % 11 === 0, $field, "{$field->getName()} shoule be the valid ISBN");// Valid if sum is divisible by 11
    }

    /**
     * Isbn13 Validation
     *
     * @param Field $field
     *
     * @return bool
     */
    public function isbn13Validation(Field $field)
    {
        $isbn = $field->getData();

        $isbn = preg_replace('/[^0-9X]/i', '', $isbn);

        if (strlen($isbn) !== 13) {
            return $this->updateFieldStatus(false, $field, "{$field->getName()} shoule be the valid ISBN"); // Invalid length
        }

        $sum = 0;

        for ($i = 0; $i < 12; $i++) {
            if (! is_numeric($isbn[$i])) {
                return $this->updateFieldStatus(false, $field, "{$field->getName()} shoule be the valid ISBN");
            }
            $sum += (int) $isbn[$i] * (($i % 2 === 0) ? 1 : 3);
        }

        $lastDigit = (int) $isbn[12];
        $checkDigit = (10 - ($sum % 10)) % 10;

        return $this->updateFieldStatus($checkDigit === $lastDigit, $field, "{$field->getName()} shoule be the valid ISBN");
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
            if ($pass_on_fail && ! $result) {
                return $flag;
            }
        }

        return $flag;
    }

    public function handleBuildInValidator(Field $field, string $rule, $params = [])
    {
        $rule = preg_replace('/Validation$/', '', $rule);
        if (ValidationConstants::isMethodValidator($rule)) {
            $rule = $rule . 'Validation';
            if (method_exists($this, $rule)) {
                if (! empty($params)) {
                    return $this->$rule($field, $params);
                }

                return $this->$rule($field);
            }
        }

        $vr = ValidationConstants::getVrObject($rule, $params);

        return $this->handleVrRule($field, $vr);
    }

    private function handleVrRule(Field $field, ValidationRule $vr)
    {
        if ($vr instanceof BaseValidator) {
            $result = $vr->validate($field);

            return $this->updateFieldStatus($result, $field, $vr->getError() ?? $field->getRuleMessage($vr->getName()));
        }

        $result = $vr->validate($field);

        return $this->updateFieldStatus($result, $field, $field->getMessage($vr->getName()) ?? 'Invalid value');
    }

    private function handleStringTypeRule(Field $field, string $rule, $params = [], $pass_on_fail = true)
    {
        if (ValidationConstants::isBuildInValidator($rule)) {
            return $this->handleBuildInValidator($field, $rule, $params);
        }

        if (ValidationConstants::isDerivedValidator($rule)) {
            $rule = ValidationConstants::getDerivedRule($rule);

            return $this->handleVrRule($field, $rule);
        }
        $params = is_array($params) ? $params : [$params];
        if (class_exists($rule)) {
            $ruleClass = ValidationConstants::getValidationObj($rule, $params);
            if ($ruleClass instanceof ValidationRule) {
                return $this->handleVrRule($field, $ruleClass);
            }
        }

        $params1 = explode(' ', $rule);
        $rule1 = array_shift($params1);
        if ($rule === $rule1) {
            throw new \Exception("Invalid rule: {$rule}");
        }
        $params = array_merge($params1, $params);

        return $this->executeRule($field, $rule1, $pass_on_fail, $params);
    }

    public function executeRule(Field $field, $rule, $pass_on_fail = true, mixed $params = [])
    {
        if ($pass_on_fail && $field->isValid() === false) {
            return false;
        }

        if (is_string($rule)) {
            return $this->handleStringTypeRule($field, $rule, $params, $pass_on_fail);
        }

        if ($rule instanceof ValidationRule) {
            return $this->handleVrRule($field, $rule);
        }

        if (is_array($rule)) {
            $params = $rule;
            $ruleName = array_shift($params);
            $params = reset($params);

            return $this->executeRule($field, $ruleName, $pass_on_fail, $params);
        }
    }

    public function validateField(Field $field, $pass_on_fail = true): bool
    {
        $flag = true;
        $rules = $field->getRules();

        foreach ($rules as $rule) {
            $result = $this->executeRule($field, $rule, $pass_on_fail);
            $flag = $flag && $result;
            if ($pass_on_fail && ! $flag) {
                return false;
            }
        }
        if ($flag === $field->isValid()) {
            return $flag;
        }

        $field->setValid(true);

        return $flag;
    }
}
