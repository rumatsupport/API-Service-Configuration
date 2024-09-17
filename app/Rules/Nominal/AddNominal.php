<?php

namespace App\Rules\Nominal;

use App\Rules\FormRequest;
use App\Rules\NominalUnique;

class AddNominal extends FormRequest
{
    public function rules($token)
    {
        return [
            'nominal' => ['required', 'numeric', new NominalUnique()],
            'satuan' => 'required|in:keping,lembar'
        ];
    }

    public function messages()
    {
        return VALIDATION_MESSAGE;
    }
}
