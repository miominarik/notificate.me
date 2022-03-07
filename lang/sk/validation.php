<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Ten :attribute musí byť akceptovaný.',
    'accepted_if' => 'Atribút :atribút musí byť prijatý, keď :other je :value.',
    'active_url' => ':attribute nie je platná adresa URL.',
    'after' => 'Atribút :atribút musí byť dátum po :date.',
    'after_or_equal' => 'Atribút :atribút musí byť dátum po alebo rovný :date.',
    'alpha' => 'Atribút :atribút musí obsahovať iba písmená.',
    'alpha_dash' => 'Atribút :atribút musí obsahovať iba písmená, čísla, pomlčky a podčiarkovníky.',
    'alpha_num' => 'Atribút :attribute musí obsahovať iba písmená a čísla.',
    'array' => 'Atribút :attribute musí byť pole.',
    'before' => 'Atribút :atribút musí byť dátum pred :date.',
    'before_or_equal' => 'Atribút : musí byť dátum pred alebo rovný :date.',
    'medzi' => [
        'array' => 'Atribút :atribút musí mať medzi :min a :max položiek.',
        'file' => 'Atribút :atribút musí byť medzi :min a :max kilobajtov.',
        'numeric' => 'Atribút :attribute musí byť medzi :min a :max.',
        'string' => 'Atribút :atribút musí byť medzi znakmi :min a :max.',
    ],
    'boolean' => 'Pole :attribute musí byť true alebo false.',
    'confirmed' => 'Potvrdenie :attribute sa nezhoduje.',
    'current_password' => 'Heslo je nesprávne.',
    'date' => ':attribute nie je platný dátum.',
    'date_equals' => 'Atribút :atribút musí byť dátum rovný :date.',
    'date_format' => 'Atribút :atribút sa nezhoduje s formátom :formát.',
    'declined' => 'Ten :attribute musí byť odmietnutý.',
    'declined_if' => 'Atribút :atribút musí byť odmietnutý, keď :other je :value.',
    'different' => 'Atribút :attribute a :other musia byť odlišné.',
    'digits' => 'Atribút :attribute musí byť :digits číslice.',
    'digits_between' => 'Atribút :atribút musí byť medzi :min a :max číslic.',
    'dimensions' => 'The :attribute má neplatné rozmery obrázka.',
    'distinct' => 'Pole :attribute má duplicitnú hodnotu.',
    'email' => 'Atribút :attribute musí byť platná e-mailová adresa.',
    'ends_with' => 'Atribút :atribút musí končiť jedným z nasledujúcich výrazov: :hodnoty.',
    'enum' => 'Vybratý atribút :attribute je neplatný.',
    'exists' => 'Vybratý atribút :attribute je neplatný.',
    'file' => ':attribute musí byť súbor.',
    'filled' => 'Pole :attribute musí mať hodnotu.',
    'gt' => [
        'array' => 'Atribút :atribút musí mať viac ako :value položiek.',
        'file' => 'Atribút :atribút musí byť väčší ako :value kilobajtov.',
        'numeric' => 'Atribút :atribút musí byť väčší ako :value.',
        'string' => 'Atribút :atribút musí byť väčší ako :value znakov.',
    ],
    'gte' => [
        'array' => 'Atribút :atribút musí mať položky :value alebo viac.',
        'file' => 'Atribút :atribút musí byť väčší alebo rovný :value kilobytes.',
        'numeric' => 'Atribút :atribút musí byť väčší alebo rovný :value.',
        'string' => 'Atribút :atribút musí byť väčší alebo rovný znakom :value.',
    ],
    'image' => 'Atribút :attribute musí byť obrázok.',
    'in' => 'Vybratý atribút :attribute je neplatný.',
    'in_array' => 'Pole :attribute v :other neexistuje.',
    'integer' => 'Atribút :attribute musí byť celé číslo.',
    'ip' => 'Atribút :attribute musí byť platná adresa IP.',
    'ipv4' => 'Atribút :attribute musí byť platná adresa IPv4.',
    'ipv6' => 'Atribút :attribute musí byť platná adresa IPv6.',
    'json' => 'Atribút :attribute musí byť platný reťazec JSON.',
    'lt' => [
        'array' => 'Atribút :atribút musí mať menej ako :value položiek.',
        'file' => 'Atribút :atribút musí byť menší ako :value kilobajtov.',
        'numeric' => 'Atribút :atribút musí byť menší ako :value.',
        'string' => 'Atribút :atribút musí byť menší ako :value znakov.',
    ],
    'lte' => [
        'array' => 'Atribút :atribút nesmie mať viac ako :value položiek.',
        'file' => 'Atribút :atribút musí byť menší alebo rovný :value kilobytes.',
        'numeric' => 'Atribút :atribút musí byť menší alebo rovný :value.',
        'string' => 'Atribút :atribút musí byť menší alebo rovný :value znakov.',
    ],
    'mac_address' => 'Atribút :attribute musí byť platná adresa MAC.',
    'max' => [
        'array' => 'Atribút :attribute nesmie mať viac ako :max položiek.',
        'file' => 'Atribút :atribút nesmie byť väčší ako :max kilobajtov.',
        'numeric' => 'Atribút :attribute nesmie byť väčší ako :max.',
        'string' => 'Atribút :atribút nesmie byť väčší ako :max znakov.',
    ],
    'mimes' => ':attribute musí byť súbor typu: :values.',
    'mimetypes' => ':attribute musí byť súbor typu: :values.',
    'min' => [
        'array' => 'Atribút :atribút musí mať aspoň :min položiek.',
        'file' => 'Atribút :atribút musí mať aspoň :min kilobajtov.',
        'numeric' => 'Atribút :atribút musí byť aspoň :min.',
        'string' => 'Atribút :atribút musí obsahovať aspoň :min znakov.',
    ],
    'multiple_of' => 'Atribút :atribút musí byť násobkom :value.',
    'not_in' => 'Vybratý',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
