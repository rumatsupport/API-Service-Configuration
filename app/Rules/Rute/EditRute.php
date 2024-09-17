<?php

namespace App\Rules\Rute;

use App\Rules\FormRequest;

class EditRute extends FormRequest
{
    public function rules($token)
    {
        $this->req->request->add(['rute_id' => $this->req->route('ruteId')]);
        return [
            'rute_id' => 'required|string|exists:App\Models\Rute,id',
            'nama' => 'required|string|unique:App\Models\Rute,nama,' . $this->req->route('ruteId'),

            'unit' => 'required|array',
            'unit.*' => 'required|string|exists:App\Models\Unit,id',
        ];
    }

    public function messages()
    {
        return VALIDATION_MESSAGE;
    }
}
