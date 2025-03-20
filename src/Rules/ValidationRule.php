<?php

namespace Validator\Rules;

interface ValidationRule
{
    /**
     * Custom validation
     *
     * @param mixed  $data Data to be validated
     * @param string $msg  String reference where the message will be stored
     *
     * @return bool|null
     */
    public function validate(mixed $data, string &$msg): ?bool;
}
