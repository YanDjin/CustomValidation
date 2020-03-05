<?php

namespace YanDjin\Validation;

/**
 * validate arrays a la Laravel
 * Class CustomValidation
 */
class CustomValidation
{
    /**
     * $validationRules : instance of validation rules class (used to validate)
     * $validationMessages : instance of validation messages class (used to generate errors messages for each validation rule and it's parameters)
     */
    private static $validationRules;
    private static $validationMessages;
    private static $valuesToValidate;

    public static $passed = true;
    public static $messages = [];

    /**
     * method that validates data in the form an associative array, with rules in the same form, calls validateOneValue for each field
     * @param array $data (associative array with to validate data)
     * @param array $rulesArray (associative array (key matches the key of field to validate in data), (values are arrays of rules of string with rules separated by pipes '|'))
     * @return bool (data validated or not)
     */
    public static function validateData(array $data, array $rulesArray): bool {
        self::$validationRules = ValidationRules::getInstance();
        self::$validationMessages = ValidationMessages::getInstance();
        if (!is_array($data) || !is_array($rulesArray))
            return false;
        self::$valuesToValidate = $data;

        self::$passed = true;
        self::$messages = [];

        foreach ($rulesArray as $key => $rules) {
            $rules = self::transformRulesToArray($rules);
            if (!self::validateOneValue($rules, $key))
                self::$passed = false;
        }
        return self::$passed;
    }

    /**
     * @param $rules
     * @return array
     */
    public static function transformRulesToArray($rules) {
        if (is_array($rules))
            return $rules;
        $rules = explode('|', $rules);
        foreach ($rules as $key => $rule) {
            $explodedRule = explode(":", $rule);
            if (count($explodedRule) == 2) {
                unset($rules[$key]);
                $rules[$explodedRule[0]] = $explodedRule[1];
            }
        }
        return $rules;
    }

    /**
     * return the all the errors messages (array of arrays(one for each field)) as a concatenated string
     * @return false|string
     */
    public static function getErrorsMessagesAsString() {
        $messageString = "";
        foreach (self::$messages as $name => $messagesArray) {
            foreach ($messagesArray as $message) {
                $messageString .= $message;
            }
            $messageString .= ", ";
        }
        if (strlen($messageString) > 2)
            $messageString = substr($messageString, 0, -2);
        return $messageString;
    }

    /**
     * checks one field with multiple rules, calls validateOneValueOneRule for each rule
     * @param $rules (array or string, see validateData doc higher)
     * @param $valueKey (key of the value)
     * @return bool (single data validated or not)
     */
    private static function validateOneValue($rules, $valueKey): bool {
        $status = true;
        $messages = [];
        foreach ($rules as $key => $value) {
            // if nullable rule is found and the field is null, return directly true event if error messages have been found before (if rule is not the first which it should be)
            if ($value == 'nullable' && (!isset(self::$valuesToValidate[$valueKey]) || self::$valuesToValidate[$valueKey] == null || strlen(self::$valuesToValidate[$valueKey]) == 0)) {
                return true;
            } elseif ($value != 'nullable') {
                if (is_string($key) && !self::validateOneValueOneRule($valueKey, $key, $value)) {
                    $messages[] = self::$validationMessages->getValidationRuleMessage($valueKey, $key, $value);
                    $status = false;
                } elseif (!is_string($key) && !self::validateOneValueOneRule($valueKey, $value)) {
                    $messages[] = self::$validationMessages->getValidationRuleMessage($valueKey, $value);
                    $status = false;
                }
            }
        }
        // add all the error messages to the main variable
        if (count($messages))
            self::$messages[$valueKey] = $messages;
        return $status;
    }

    /**
     * checks one data field with one rule
     * @param string $valueKey (value identifier in the data and rule array)
     * @param string $rule (rule name)
     * @param null $ruleParameter (rule parameter (ex: min 5 characters, 5 is the parameter))
     * @return bool (single data with single rule validated or not)
     */
    private static function validateOneValueOneRule(string $valueKey, string $rule, $ruleParameter = null) {
        if (!isset(self::$valuesToValidate[$valueKey]))
            return false;
        // if rule parameter is not null, call method with the name of the rule and rule parameter as parameter
        if (!is_null($ruleParameter)) {
            if (method_exists(self::$validationRules, $rule)) {
                return self::$validationRules->{$rule}(self::$valuesToValidate[$valueKey], $ruleParameter);
            }
            return false;
        }
        if (method_exists(self::$validationRules, $rule))
            return self::$validationRules->$rule(self::$valuesToValidate[$valueKey]);
        return false;
    }
}