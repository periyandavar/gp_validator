<?php

namespace Validator;

use Loader\Container;
use Validator\Rules\ConfirmationValidator;
use Validator\Rules\DataTypeValidator;
use Validator\Rules\DateValidator;
use Validator\Rules\LengthValidator;
use Validator\Rules\NumericValidator;
use Validator\Rules\UniqueValidator;
use Validator\Rules\ValidationRule;

class ValidationConstants
{
    public const REQUIRED_VALIDATOR = 'required';
    public const INTEGER_VALIDATOR = 'integer';
    public const FLOAT_VALIDATOR = 'float';
    public const STRING_VALIDATOR = 'string';
    public const BOOLEAN_VALIDATOR = 'boolean';
    public const ARRAY_VALIDATOR = 'array';
    public const NULL_VALIDATOR = 'null';
    public const DATA_TYPE_VALIDATOR = 'data-type';
    public const NUMERIC_VALIDATOR = 'numeric';
    public const ALPHA_VALIDATOR = 'alpha';
    public const ALPHA_NUMERIC_VALIDATOR = 'alphaNumeric';
    public const LENGTH_VALIDATOR = 'length';
    public const LANDLINE_VALIDATOR = 'landline';
    public const VALUES_IN_VALIDATOR = 'valuesIn';
    public const VALUES_NOT_IN_VALIDATOR = 'valuesNotIn';
    public const REGEX_VALIDATOR = 'regex';
    public const UNIQUE_VALIEATOR = 'unique';
    public const CONFIRMATION_VALIDATIOR = 'confirmation';
    public const URL_VALIDATOR = 'url';
    public const EMAIL_VALIDATOR = 'email';
    public const DATE_VALIDATOR = 'date';
    public const MOBILE_NUMBER_VALIDATIOR = 'mobileNumber';
    public const ALPHA_SPACE_VALIDATOR = 'alphaspace';
    public const ISBN_VALIDATOR = 'isbn';
    public const ISBN_10_VALIDATOR = 'isbn10';
    public const ISBN_13_VALIDATOR = 'isbn13';
    public const POSITIVE_NUMBER_VALIDATOR = 'positiveNumber';

    public const METHOD_VALIDATORS = [
        self::URL_VALIDATOR,
        self::VALUES_IN_VALIDATOR,
        self::VALUES_NOT_IN_VALIDATOR,
        self::MOBILE_NUMBER_VALIDATIOR,
        self::LANDLINE_VALIDATOR,
        self::ALPHA_SPACE_VALIDATOR,
        self::EMAIL_VALIDATOR,
        self::ISBN_VALIDATOR,
        self::ISBN_10_VALIDATOR,
        self::ISBN_13_VALIDATOR,
        self::POSITIVE_NUMBER_VALIDATOR,
        self::REGEX_VALIDATOR,
        self::REQUIRED_VALIDATOR,
        self::ALPHA_VALIDATOR,
        self::ALPHA_NUMERIC_VALIDATOR,
        self::POSITIVE_NUMBER_VALIDATOR,
    ];

    public const CLASS_VALIDATORS = [
        self::DATA_TYPE_VALIDATOR => DataTypeValidator::class,
        self::NUMERIC_VALIDATOR => NumericValidator::class,
        self::LENGTH_VALIDATOR => LengthValidator::class,
        self::UNIQUE_VALIEATOR => UniqueValidator::class,
        self::CONFIRMATION_VALIDATIOR => ConfirmationValidator::class,
        self::DATE_VALIDATOR => DateValidator::class,
    ];

    public const DERIVED_VALIDATORS = [
        self::INTEGER_VALIDATOR => [self::DATA_TYPE_VALIDATOR, 'integer'],
        self::FLOAT_VALIDATOR => [self::DATA_TYPE_VALIDATOR, 'float'],
        self::STRING_VALIDATOR => [self::DATA_TYPE_VALIDATOR, 'string'],
        self::BOOLEAN_VALIDATOR => [self::DATA_TYPE_VALIDATOR, 'boolean'],
        self::ARRAY_VALIDATOR => [self::DATA_TYPE_VALIDATOR, 'array'],
        self::NULL_VALIDATOR => [self::DATA_TYPE_VALIDATOR, ['null']],
    ];

    public static function isDerivedValidator(string $name)
    {
        $base_validator = self::DERIVED_VALIDATORS[$name] ?? [''];

        return self::isClassValidator(reset($base_validator));
    }

    public static function getBuildInValidators()
    {
        return array_merge(self::METHOD_VALIDATORS, array_keys(self::CLASS_VALIDATORS));
    }

    public static function isClassValidator(string $name)
    {
        return isset(self::CLASS_VALIDATORS[$name]);
    }

    public static function isMethodValidator(string $name)
    {
        return in_array($name, self::METHOD_VALIDATORS);
    }

    public static function getDerivedRule(string $name)
    {
        if (! self::isDerivedValidator($name)) {
            return null;
        }

        [$validator, $args] = self::DERIVED_VALIDATORS[$name];

        $args = is_array($args) ? $args : [$args];

        return self::getVrObject($validator, $args);
    }

    public static function getVrObject($name, array $params = [])
    {
        return self::getValidationObj(
            self::CLASS_VALIDATORS[$name],
            $params
        );
    }

    public static function isBuildInValidator(string $name)
    {
        $rule = preg_replace('/Validation$/', '', $name);

        return in_array($rule, self::getBuildInValidators());
    }

    public static function getValidationObj(string $rule, array $params = []): ?ValidationRule
    {
        if (class_exists($rule)) {
            $keys = array_keys($params);
            if ($keys === range(0, count($params) - 1)) {
                return new $rule(...$params);
            }

            return Container::resolveClassConstructor($rule, $params);
        }

        return null;
    }
}
