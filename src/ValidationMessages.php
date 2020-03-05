<?php

namespace YanDjin\Validation;

class ValidationMessages
{
    static $instance = null;

    private function __construct() {
    }

    public static function getInstance() {
        if (self::$instance === null)
            self::$instance = new ValidationMessages();
        return self::$instance;
    }

    const VALIDATION_MESSAGES = [
        'email' => "is not a valid email",
        'number' => 'is not a number',
        'characters' => 'is not characters only',
        'required' => 'must not be null'
    ];

    /**
     * checks if message for the rule is found in the constant, else calls method with the name of the rule to return the constructed message (if it exists), else return the $fieldName is not valid;
     * @param $fieldName
     * @param $rule
     * @param null $ruleParameter
     * @return string
     */
    public function getValidationRuleMessage($fieldName, $rule, $ruleParameter = null) {
        if (is_null($ruleParameter) && isset(self::VALIDATION_MESSAGES[$rule]))
            $messages = ($fieldName . " " . self::VALIDATION_MESSAGES[$rule]);
        elseif (method_exists(self::class, $rule)) {
            if (!is_null($ruleParameter)) {
                $messages = $this->{$rule}($fieldName, $ruleParameter);
            } else {
                $messages = $this->{$rule}($fieldName);
            }
        } else {
            $messages = "$fieldName is not valid";
        }
        return $messages;
    }

    private function min($fieldName, $min) {
        return "$fieldName must be at minimum $min characters long";
    }

    private function max($fieldName, $max) {
        return "$fieldName must be at minimum $max characters long";
    }
}