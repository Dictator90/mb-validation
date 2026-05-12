<?php

namespace MB\Validation\Registry;

use MB\Validation\Rules\AcceptedRule;
use MB\Validation\Rules\AlphaDashRule;
use MB\Validation\Rules\AlphaNumRule;
use MB\Validation\Rules\AlphaRule;
use MB\Validation\Rules\AnyOfRule;
use MB\Validation\Rules\ArrayRule;
use MB\Validation\Rules\AsciiRule;
use MB\Validation\Rules\BetweenRule;
use MB\Validation\Rules\BooleanRule;
use MB\Validation\Rules\ConfirmedRule;
use MB\Validation\Rules\ContainsRule;
use MB\Validation\Rules\DateFormatRule;
use MB\Validation\Rules\DateRule;
use MB\Validation\Rules\DecimalRule;
use MB\Validation\Rules\DeclinedRule;
use MB\Validation\Rules\DifferentRule;
use MB\Validation\Rules\DigitsBetweenRule;
use MB\Validation\Rules\DigitsRule;
use MB\Validation\Rules\DoesntContainRule;
use MB\Validation\Rules\DoesntEndWithRule;
use MB\Validation\Rules\DoesntStartWithRule;
use MB\Validation\Rules\EmailRule;
use MB\Validation\Rules\EndsWithRule;
use MB\Validation\Rules\Exists;
use MB\Validation\Rules\FilledRule;
use MB\Validation\Rules\HexColorRule;
use MB\Validation\Rules\InRule;
use MB\Validation\Rules\IntegerRule;
use MB\Validation\Rules\IpRule;
use MB\Validation\Rules\Ipv4Rule;
use MB\Validation\Rules\Ipv6Rule;
use MB\Validation\Rules\JsonRule;
use MB\Validation\Rules\ListRule;
use MB\Validation\Rules\LowercaseRule;
use MB\Validation\Rules\MacAddressRule;
use MB\Validation\Rules\MaxRule;
use MB\Validation\Rules\MinRule;
use MB\Validation\Rules\NotInRule;
use MB\Validation\Rules\NotRegexRule;
use MB\Validation\Rules\NumericRule;
use MB\Validation\Rules\ProhibitedRule;
use MB\Validation\Rules\RegexRule;
use MB\Validation\Rules\RequiredArrayKeysRule;
use MB\Validation\Rules\RequiredIfRule;
use MB\Validation\Rules\RequiredRule;
use MB\Validation\Rules\RequiredUnlessRule;
use MB\Validation\Rules\SameRule;
use MB\Validation\Rules\SizeRule;
use MB\Validation\Rules\StartsWithRule;
use MB\Validation\Rules\StringRule;
use MB\Validation\Rules\TimezoneRule;
use MB\Validation\Rules\UlidRule;
use MB\Validation\Rules\Unique;
use MB\Validation\Rules\UppercaseRule;
use MB\Validation\Rules\UrlRule;
use MB\Validation\Rules\UuidRule;

final class DefaultRuleRegistrar
{
    public static function register(): void
    {
        RuleRegistry::register(
            AcceptedRule::class,
            AlphaDashRule::class,
            AlphaNumRule::class,
            AlphaRule::class,
            AnyOfRule::class,
            ArrayRule::class,
            AsciiRule::class,
            BetweenRule::class,
            BooleanRule::class,
            ConfirmedRule::class,
            ContainsRule::class,
            DateFormatRule::class,
            DateRule::class,
            DecimalRule::class,
            DeclinedRule::class,
            DifferentRule::class,
            DigitsBetweenRule::class,
            DigitsRule::class,
            DoesntContainRule::class,
            DoesntEndWithRule::class,
            DoesntStartWithRule::class,
            EmailRule::class,
            EndsWithRule::class,
            Exists::class,
            FilledRule::class,
            HexColorRule::class,
            InRule::class,
            IntegerRule::class,
            IpRule::class,
            Ipv4Rule::class,
            Ipv6Rule::class,
            JsonRule::class,
            ListRule::class,
            LowercaseRule::class,
            MacAddressRule::class,
            MaxRule::class,
            MinRule::class,
            NotInRule::class,
            NotRegexRule::class,
            NumericRule::class,
            ProhibitedRule::class,
            RegexRule::class,
            RequiredArrayKeysRule::class,
            RequiredIfRule::class,
            RequiredRule::class,
            RequiredUnlessRule::class,
            SameRule::class,
            SizeRule::class,
            StartsWithRule::class,
            StringRule::class,
            TimezoneRule::class,
            UlidRule::class,
            Unique::class,
            UppercaseRule::class,
            UrlRule::class,
            UuidRule::class,
        );
    }
}
