<?php

namespace App\Http\Controllers\OpenAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// models
use App\Models\Unit as UnitDB;

class UnitController extends Controller
{
    private static $jsonData, $myId, $roleId, $unitId, $token;

    public function __construct(Request $request)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';
    }

    public function getAll(Request $request)
    {
        try {
            $unit = UnitDB::where(
                [
                    'status' => '1',
                    'is_show_mobile' => '1'
                ]
            )
                ->orderBy('kode', 'ASC')
                ->get()
                ->toArray();
            $data = [];

            if (!empty($unit)) {
                foreach ($unit as $key => $value) {
                    $data[] = [
                        'kode' => $unit[$key]['kode'],
                        'nama' => $unit[$key]['nama'],
                        'alamat' => $unit[$key]['alamat'],
                        'telp' => $unit[$key]['telp'],
                        'fax' => $unit[$key]['fax'],
                        'is_show_mobile' => $unit[$key]['is_show_mobile'],
                        'latitude' => $unit[$key]['latitude'],
                        'longitude' => $unit[$key]['longitude'],
                        'country_id' => '1',
                        'province_id' => '1',
                        'city_district_id' => '1',
                        'subdistrict_id' => '1',
                        'village_subdistrict_id' => '1'
                    ];
                }
            }
            self::$jsonData['status'] = 200;
            self::$jsonData['data'] = $data;
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }
}
