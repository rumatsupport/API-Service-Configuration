<?php

namespace App\Rules\Regional;

use App\Rules\FormRequest;

class EditRegional extends FormRequest
{
    public function rules($token)
    {
        $this->req->request->add(['regional_id' => $this->req->route('regionalId')]);
        return [
            'regional_id' => 'required|string|exists:App\Models\Regional,id',
            'kode' => 'required|string|unique:App\Models\Regional,kode,'.$this->req->route('regionalId'),
            'nama' => 'required|string|unique:App\Models\Regional,nama,'.$this->req->route('regionalId'),
            'alamat' => 'required|string|unique:App\Models\Regional,alamat,'.$this->req->route('regionalId'),
            'telp' => 'required|array',
                'telp.*' => 'required|numeric|digits_between:8,13|starts_with:0',
            'email' => 'required|string|email|unique:App\Models\Regional,email,'.$this->req->route('regionalId'),
            'status' => 'required|string|in:0,1',
            'area_asmen_id' => 'string|exists:App\Models\Asmen,id',
        ];
    }

    public function messages()
    {
        return VALIDATION_MESSAGE;
    }
}
