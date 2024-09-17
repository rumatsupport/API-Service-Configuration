<?php

namespace App\Rules\Regional;

use App\Rules\FormRequest;

class AddRegional extends FormRequest
{
    public function rules($token)
    {
        return [
            'kode' => 'required|string|unique:App\Models\Regional,kode',
            'nama' => 'required|string|unique:App\Models\Regional,nama',
            'alamat' => 'required|string|unique:App\Models\Regional,alamat',
            'telp' => 'required|array',
            'telp.*' => 'required|numeric|digits_between:8,13|starts_with:0',
            'email' => 'required|string|email|unique:App\Models\Regional,email',
            'status' => 'required|string|in:0,1',
            'area_asmen_id' => 'string|exists:App\Models\Asmen,id',
        ];
    }

    public function messages()
    {
        return VALIDATION_MESSAGE;
    }
}
