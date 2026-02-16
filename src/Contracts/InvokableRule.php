<?php

namespace MB\Validation\Contracts;

/**
 * Rule that is invoked with attribute, value, and parameters (string rule style).
 * ValidationRule is the primary interface; this alias is for parser compatibility.
 */
interface InvokableRule extends ValidationRule
{
}
