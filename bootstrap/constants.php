<?php

define('URL_AUTH', [
    'validateToken' => '/api/auth/v2/validate-token',

    // user account
    'user' => '/api/auth/v2/user'
]);


define('VALIDATION_MESSAGE', [
    'required' => ':attribute Harus di isi',
    'numeric' => ':attribute Harus berupa angka',
    'array' => ':attribute Harus dalam bentuk array',
    'exists' => ':attribute :input tidak di temukan',
    'unique' => ':attribute :input sudah terpakai',
    'in' => ':attribute harus antara :values',
    'required_if' => ':attribute harus di isi jika :other = :value',
    'min' => ':attribute minimal :min',
    'max' => ':attribute maximal :max',
    'date' => ':attribute harus dalam bentuk tanggal',
    'date_format' => ':attribute harus dalam format :format',
    'after' => ':attribute harus tanggal lebih besar dari tanggal :date',
    'starts_with' => ':attribute harus di awali dengan :values',
    'digits_between' => ':attribute harus antara :min sampai :max digit'
]);
