<?php

namespace App\Rules\Bank;

use App\Rules\FormRequest;

class AddBank extends FormRequest
{
    public function rules($token)
    {
        return [
            'nama' => 'required|string|unique:App\Models\Bank,nama',
            'kode' => 'required|string|unique:App\Models\Bank,kode',
            'kode_num' => 'required|string|unique:App\Models\Bank,kode_num'
        ];
    }

    public function messages()
    {
        return VALIDATION_MESSAGE;
    }
}
