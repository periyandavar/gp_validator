# GP Validator Library

This library provides a robust validation engine to validate data inputs based on various rules. The library is modular and easily extensible, allowing you to create and use custom validation rules. which has various built in validation rules for common use cases (e.g., email, mobile number, numeric validation) also support for custom validation rules.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Getting Started](#getting-started)
- [Features](#features)
- [Classes](#classes)
  - [Field](#field)
  - [Fields](#fields)
  - [ValidationEngine](#validationengine)
  - [validationRule](#validationengine)
- [Usage](#usage)
  - [Creating a Validation Field](#1-creating-a-validation-field)
  - [Validating a Single Field](#2-validating-a-single-field)
  - [Grouping Fields for Validation](#3-grouping-fields-for-validation)
  - [Validating All Fields in a Group](#4-validating-all-fields-in-a-group)
    - [using the fields](#using-the-fields)
    - [Using the ValidationEngine](#using-the-validationengine)
  - [Built-in Validation Rules](#5-built-in-validation-rules)
  - [Creating Custom Validation Rules](#6-creating-custom-validation-rules)
- [Example](#example)
- [Contributing](#contributing)
- [License](#license)
- [Author](#author)
- [Contact](#contact)

---

## Requirements

- PHP 7.3.5 or higher
- composer

---

## Installation

You can install `gp_validator` using Composer. Run the following command in your terminal:

```
composer require gp/validator
```
---

## Getting Started

After installation, you can start using the package by including the autoloader:

```
require 'vendor/autoload.php';
```
---

## Features

- Field-Based Validation: Validate individual fields with customizable rules and error messages.
- Add Multiple rule for the field: Ability to define multiple rules for a single field.
- Supports field-level and group-level validations.
- Bulk Validation: Validate multiple fields at once using the Fields collection.
- Predefined Validation Rules: Out-of-the-box support for common validation rules such as:
    - Email validation
    - Numeric validation
    - Mobile number validation
    - Regular expression-based validation
    - Length validation
- Custom Validation Rules: Extend the validation engine with your own rules by implementing the ValidationRule interface.
- Dynamic Rule Assignment: Assign different validation rules dynamically to fields at runtime.
- Error Management: Retrieve detailed error messages for invalid fields during validation.
- Dependency-Fre: Lightweight and does not depend on external libraries, ensuring easy integration with any PHP project.
- PSR-4 Compatible: Fully compatible with PSR-4 autoloading standards for seamless integration into modern PHP applications.
- Extensible Architecture: Designed with extensibility in mind, allowing developers to easily add features or customize behavior.
- Developer-Friendly API: Intuitive and easy-to-use API for managing fields, rules, and validations.
- Validation Continuation: Optional configuration to continue validating other rules even if one rule fails.
- Regex Support: Built-in support for regular expression-based validations for flexible rule definition.
- Field-Level Rule Chaining: Support for chaining multiple validation rules for a single field.
- Error Aggregation: Collect and display validation errors from all fields in a single operation.


---

## Classes

### Field

#### Methods

| Method                          | Description                                          |
|---------------------------------|------------------------------------------------------|
| `getName()`                     | Returns the field's name.                           |
| `getData()`                     | Returns the field's data.                           |
| `getRules()`                    | Returns the validation rules associated with the field. |
| `getErrors()`                   | Returns the errors for the field.                   |
| `validate()`                    | Validates the field using the applied rules.        |
| `addRule(string $rule)`         | Adds a validation rule to the field.                |
| `addError(string $error)`       | Adds an error message to the field.                 |

---

### Fields

#### Methods

| Method                          | Description                                          |
|---------------------------------|------------------------------------------------------|
| `addField(Field $field)`        | Adds a single field to the collection.              |
| `addFields(Field ...$fields)`   | Adds multiple fields to the collection.             |

---

### ValidationEngine

#### Methods

| Method                          | Description                                          |
|---------------------------------|------------------------------------------------------|
| `validate(Fields $fields)`      | Validates all fields in the group.                  |
| `validateField(Field $field)`   | Validates a single field.                           |

---

### ValidationRule

#### Method



| Method                          | Description                                          |
|---------------------------------|------------------------------------------------------|
| validate(string $data, string &$msg): ?bool | Validates the provided data and Returns resulthe method also populates 
||the $msg parameter with an error message if validation fails. |

## Usage

### 1. Creating a Validation Field

To validate a single field, create an instance of the `Field` class:

```
use Validator\Field\Field;

$field = new Field('email', 'user@example.com', ['emailValidation']);
```

### 2. Validating a Single Field

Use the `validate()` method on a `Field` instance:

```
$isValid = $field->validate();

if ($isValid) {
  echo "The field is valid."; 
} else {
  echo "The field is invalid. Errors: " . implode(', ', $field->getErrors());
}

```
### 3. Grouping Fields for Validation

You can group multiple fields using the `Fields` class:

```

use Validator\Field\Fields;

$fields = new Fields([
    new Field('email', 'user@example.com', ['emailValidation']),
    new Field('mobile', '9876543210', ['mobileNumberValidation']),
]);

```

### 4. Validating All Fields in a Group

#### using the `fields` 

```
$isValid = $fields->validate();

if ($isValid) {
  echo "All fields are valid.";
} else {
  print_r($fields->getErrors()); // print errors
  print_r($fields->getInvalidFields());// print invalidFieldDetails
}
```

#### Using the `ValidationEngine`

```
use Validator\ValidationEngine;

$validationEngine = new ValidationEngine(); $invalidFieldDetails = [];
$isValid = $validationEngine->validate($fields, true, $invalidFieldDetails);

if ($isValid) {
  echo "All fields are valid.";
} else {
  print_r($invalidFieldDetails); // View details of invalid fields
}

```

### 5. Built-in Validation Rules

The following validation rules are included in the library:

| Rule Name                | Description                                            |
|--------------------------|--------------------------------------------------------|
| `emailValidation`        | Validates that the field contains a valid email.       |
| `mobileNumberValidation` | Validates that the field contains a valid mobile number in India. |
| `landlineValidation`     | Validates Indian landline numbers.                    |
| `alphaSpaceValidation`   | Validates that the field contains only alphabets and spaces. |
| `isbnValidation`         | Validates that the field contains a valid ISBN-10.    |
| `numericValidation`      | Validates that the field contains a numeric value and optionally checks a range. |
| `positiveNumberValidation` | Validates that the field contains a positive number. |
| `regexValidation`        | Validates data using a custom regular expression.     |
| `lengthValidation`       | Validates the length of the data. Supports min and max length. |
| `requiredValidation`     | Validates that the field is not empty.                |

### 6. Creating Custom Validation Rules

To create a custom validation rule, implement the `ValidationRule` interface:

```

use System\Library\ValidationRule;

class CustomRule implements ValidationRule
{
    public function validate(string $data, string &$msg): ?bool
    {
        if ($data === 'custom') {
            return true;
        }

$msg = "The value must be 'custom'.";
        return false;
    }
}

```

Then, apply the custom rule to a field:

```
$field = new Field('example', 'custom', [CustomRule::class]); $field->validate();
```

---
## Example

Here's a full example of validating multiple fields:

```
use Validator\Field\Field;
use Validator\Field\Fields;
use Validator\ValidationEngine;

$fields = new Fields([
    new Field('email', 'user@example.com', ['emailValidation']),
    new Field('age', 25, ['numericValidation']),
]);

$validationEngine = new ValidationEngine(); $invalidFields = [];
$isValid = $validationEngine->validate($fields, true, $invalidFields);

if ($isValid) {
  echo "All fields are valid.";
} else {
  print_r($invalidFields); // Display invalid field details
}

```

---


## Contributing

Contributions are welcome! If you would like to contribute to gp_validator, please follow these steps:

- Fork the repository.
- Create a new branch (git checkout -b feature/- YourFeature).
- Make your changes and commit them (git commit -m 'Add some feature').
- Push to the branch (git push origin feature/YourFeature).
- Open a pull request.
- Please ensure that your code adheres to the coding standards and includes appropriate tests.

---

## License

This package is licensed under the MIT License. See the [LICENSE](https://github.com/periyandavar/gp_validator/blob/main/LICENSE) file for more information.

---

## Contact
For questions or issues, please reach out to the development team or open a ticket.

---


## Author

- Periyandavar [Github](https://github.com/periyandavar) (<vickyperiyandavar@gmail.com>)

---