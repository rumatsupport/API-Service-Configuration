<?php

namespace App\Rules\Unit;

use App\Rules\FormRequest;

// rules
use App\Rules\Regex;

class EditUnit extends FormRequest
{
    public function rules($token)
    {
        $this->req->request->add(['unit_id' => $this->req->route('unitId')]);
        return [
            'unit_id' => 'required|string|exists:App\Models\Unit,id',
            'regional_id' => 'nullable|string|exists:App\Models\Regional,id',
            'kode' => 'required|string|unique:App\Models\Unit,kode,' . $this->req->route('unitId'),
            'nama' => 'required|string|unique:App\Models\Unit,nama,' . $this->req->route('unitId'),
            'alamat' => 'required|string|unique:App\Models\Unit,alamat,' . $this->req->route('unitId'),

            'telp' => 'required|array',
            'telp.*' => 'required|numeric|digits_between:8,13|starts_with:0',

            'fax' => 'nullable|numeric|starts_with:0',
            'milik' => 'required|in:Milik,Kerjasama,Mitra',
            'target' => 'required|numeric',
            'is_show_mobile' => 'required|in:0,1',
            'latitude' => [
                'required_if:is_show_mobile,1', 'nullable', 'unique:App\Models\Unit,latitude,' . $this->req->route('unitId'), new Regex('latitude')
            ],
            'longitude' => [
                'required_if:is_show_mobile,1', 'nullable', 'unique:App\Models\Unit,longitude,' . $this->req->route('unitId'), new Regex('longitude')
            ],
            'status' => 'required|string|in:0,1',

            'building' => 'required',
            'building.nama' => 'required|string',
            'building.pemilik' => 'required|string',

            'building.telp_pemilik' => 'required|array',
            'building.telp_pemilik.*' => 'required|numeric|digits_between:8,13|starts_with:0',

            'building.npwp_pemilik' => 'required|string',
            'building.rekening' => 'required|string',
            'building.rekening_nama' => 'required|string',

            'building.pic' => 'required|string',
            'building.telp_pic' => 'required|array',
            'building.telp_pic.*' => 'required|numeric|digits_between:8,13|starts_with:0',

            'building.building_status' => 'required',
            'building.building_status.status' => 'required|in:Sewa,Bagi Hasil',

            'building.building_status.harga_sewa' => 'required_if:building.building_status.status,Sewa|present|nullable|numeric',
            'building.building_status.satuan_sewa' => 'required_if:building.building_status.status,Sewa|present|nullable|in:Bulan,Tahun',

            'building.building_status.pembagi_hasil' => 'required_if:building.building_status.status,Bagi Hasil|present|nullable|string',
            'building.building_status.minimum_charge' => 'required_if:building.building_status.status,Bagi Hasil|present|nullable|numeric',

            'building.building_status.persentase_jasa' => 'present|nullable|numeric',
            'building.building_status.persentase_dressing' => 'present|nullable|numeric',
            'building.building_status.persentase_bhp' => 'present|nullable|numeric',
            'building.building_status.pajak' => 'present|nullable|numeric'
        ];
    }

    public function messages()
    {
        return VALIDATION_MESSAGE;
    }
}
