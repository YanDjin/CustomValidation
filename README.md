# Small and simple validation package inspired by laravel validation, no dependencies

## how to install :

```php
composer require yandjin/custom-validation
```

## how to use

use the following namespace :

```php
use YanDjin\Validation
```

the validation returns true or false :

```php
$arrayOfData = [
    "name" => "YanDjin",
    "age" => 26
];

if(!CustomValidation::validateData($arrayOfData, [
    "name" => "characters",
    "age" => "numeric|min:18" // pipe the validation rules (: for parameters ) or use array "age" => ["numeric", "min" => 18]
]) {
    // returns the fields not passing the validation and the errors
    return CustomValidation::getErrorsMessagesAsString();
}
```
