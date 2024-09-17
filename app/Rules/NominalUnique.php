<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

// Helper
use App\Models\Nominal;

class NominalUnique implements Rule
{
    public function __construct($parameters = array())
    {
        $this->message = '';
    }

    public function passes($attribute, $value)
    {
        if (request()->has('nominal_id')) {
            $param = [
                ['nominal', '=', $value],
                ['tahun' ,'=', date('Y')],
                ['satuan' ,'=', request()->get('satuan')],
                ['id', '!=', request()->get('nominal_id')]
            ];
            $check = Nominal::where($param)->first();
            if (!empty($check)) {
                $this->message = 'Nominal '.$value.' - '.request()->get('satuan').' Sudah Ada';
                return false;
            }
        } else {
            $param = [
                'nominal' => $value,
                'tahun' => date('Y'),
                'satuan' => request()->get('satuan')
            ];
            $check = Nominal::where($param)->first();
            if (!empty($check)) {
                $this->message = 'Nominal '.$value.' - '.request()->get('satuan').' Sudah Ada';
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
