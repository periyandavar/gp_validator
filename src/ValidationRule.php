<?php

namespace System\Library;

interface ValidationRule
{
    /**
     * Custom validation
     *
     * @param string $data Data to be validated
     * @param string $msg  String reference where the message will be stored
     *
     * @return bool|null
     */
    public function validate(string $data, string &$msg): ?bool;
}
