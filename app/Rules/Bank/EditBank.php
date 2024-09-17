<?php

namespace App\Rules\Bank;

use App\Rules\FormRequest;

class EditBank extends FormRequest
{
    public function rules($token)
    {
        $this->req->request->add(['bank_id' => $this->req->route('bankId')]);
        return [
            'bank_id' => 'required|string|exists:App\Models\Bank,id',
            'nama' => 'required|string|unique:App\Models\Bank,nama,'.$this->req->route('bankId'),
            'kode' => 'required|string|unique:App\Models\Bank,kode,'.$this->req->route('bankId'),
            'kode_num' => 'required|string|unique:App\Models\Bank,kode_num,'.$this->req->route('bankId')
        ];
    }

    public function messages()
    {
        return VALIDATION_MESSAGE;
    }
}
