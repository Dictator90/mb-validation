<?php

namespace MB\Validation\Contracts;

interface ValidatorAwareRule
{
    /**
     * Set the current validator.
     *
     * @param  ValidatorInterface  $validator
     * @return $this
     */
    public function setValidator(ValidatorInterface $validator);
}
