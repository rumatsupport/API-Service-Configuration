<?php

namespace App\Rules\Asmen;

use App\Rules\FormRequest;

class Edit extends FormRequest
{
    public function rules($token)
    {
        $this->req->request->add(['asmen_area_id' => $this->req->route('asmenAreaId')]);
        return [
            'asmen_area_id' => 'required|string|exists:App\Models\Asmen,id',
            'kode' => 'required|string|unique:App\Models\Asmen,kode,'.$this->req->route('asmenAreaId'),
            'nama' => 'required|string|unique:App\Models\Asmen,nama,'.$this->req->route('asmenAreaId'),
        ];
    }

    public function messages()
    {
        return VALIDATION_MESSAGE;
    }
}
