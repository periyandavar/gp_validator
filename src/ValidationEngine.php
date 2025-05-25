<?php

namespace Validator;

use Validator\Exception\ValidationException;
use Validator\Field\Field;
use Validator\Field\Fields;
use Validator\Rules\BaseValidator;
use Validator\Rules\ValidationRule;

class ValidationEngine
{
    use Validator;

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
            throw new ValidationException("Invalid rule: {$rule}", ValidationException::INVALID_RULE);
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
