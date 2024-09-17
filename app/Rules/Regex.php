<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Regex implements Rule
{
    public function __construct(string $regex)
    {
        $this->message = '';
        $this->regex = $regex;
    }

    public function passes($attribute, $value)
    {
        // collect telp data
        if ($this->regex == 'latitude') {
            $rule = [
                $attribute => [
                    // source : https://github.com/mattkingshott/axiom/blob/master/src/Rules/LocationCoordinates.php
                    'regex:/^[-]?((([0-8]?[0-9])(\.(\d{1,8}))?)|(90(\.0+)?))$/'
                ]
            ];
        }

        elseif ($this->regex == 'longitude') {
            $rule = [
                $attribute => [
                    // source : https://github.com/mattkingshott/axiom/blob/master/src/Rules/LocationCoordinates.php
                    'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))(\.(\d{1,8}))?)|180(\.0+)?)$/'
                ]
            ];
        }

        else {
            $rule = [
                $attribute => [
                    'regex:'.$this->regex
                ]
            ];
        }

        $dataRule = [
            $attribute => $value
        ];

        $validation = Validator::make($dataRule, $rule, VALIDATION_MESSAGE);
        if ($validation->fails()) {
            $this->message = $validation->errors()->first();
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
