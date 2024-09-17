<?php

namespace App\Rules\Nominal;

use App\Rules\FormRequest;
use App\Rules\NominalUnique;

class EditNominal extends FormRequest
{
    public function rules($token)
    {
        $this->req->request->add(['nominal_id' => $this->req->route('nominalId')]);
        return [
            'nominal_id' => 'required|string|exists:App\Models\Nominal,id',
            'nominal' => ['required', 'numeric', new NominalUnique()],
            'satuan' => 'required|in:keping,lembar',
            'status' => 'required|string|in:0,1'
        ];
    }

    public function messages()
    {
        return VALIDATION_MESSAGE;
    }
}
