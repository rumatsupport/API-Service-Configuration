<?php

namespace App\Rules\Asmen;

use App\Rules\FormRequest;

class Add extends FormRequest
{
    public function rules($token)
    {
        return [
            'kode' => 'required|string|unique:App\Models\Asmen,kode',
            'nama' => 'required|string|unique:App\Models\Asmen,nama',
        ];
    }

    public function messages()
    {
        return VALIDATION_MESSAGE;
    }
}
