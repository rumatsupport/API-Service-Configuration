<?php

namespace App\Rules\Rute;

use App\Rules\FormRequest;

class AddRute extends FormRequest
{
    public function rules($token)
    {
        return [
            'nama' => 'required|string|unique:App\Models\Rute,nama',

            'unit' => 'required|array',
            'unit.*' => 'required|string|exists:App\Models\Unit,id',
        ];
    }

    public function messages()
    {
        return VALIDATION_MESSAGE;
    }
}
