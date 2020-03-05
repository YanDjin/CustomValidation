<?php

namespace YanDjin\Validation;

class ValidationRules
{
    static $instance = null;

    private function __construct() {
    }

    public static function getInstance() {
        if (self::$instance === null)
            self::$instance = new ValidationRules();
        return self::$instance;
    }

    public function email($value) {
        return preg_match(
            '/^(?:[a-z0-9!#$%&\'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/',
            $value
        );
    }

    public function number($value) {
        if (is_numeric($value))
            return true;
        return preg_match(
            '/^[0-9]+$/',
            $value
        );
    }

    public function characters($value) {
        return preg_match(
            '/^[^0-9]+$/',
            $value
        );
    }

    public function required($value) {
        return ($value != null && strlen($value) != 0);
    }

    public function min($value, $min) {
        if (preg_match('/^[0-9]+$/', $value)) {
            if ((double)($value) >= (double)($min))
                return true;
        } else {
            if (strlen($value) >= (double)($min))
                return true;
        }
        return false;
    }

    public function regex($value, $regex) {
        return preg_match($regex, $value);
    }
}