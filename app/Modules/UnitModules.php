<?php

namespace App\Modules;

// vendor
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

// models
use App\Models\Unit;
use App\Models\Asmen;
use App\Models\Regional;

// query
use App\Query\UnitQuery;
use App\Query\BuildingQuery;
use App\Query\BuildingStatusQuery;

class UnitModules
{
    private static $jsonData, $responseUnit, $dataUnit;

    public static function getAll(object $request)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        try {
            $param = $request->query();

            $unit = new Unit();
            $column = $unit->getTableColumns();

            $column = array_diff($column, ['id', 'created_at', 'updated_at', 'updated_by']);
            $column = array_merge($column, ['page', 'pagination']);

            // return false if query param not in list
            foreach ($param as $key => $value) {
                if (!in_array($key, $column)) {
                    throw new \Exception('Unknown ' . $key . ' in query param list', 400);
                }
            }

            $pagination = isset($param['pagination']) && $param['pagination'] != null ? $param['pagination'] : true;
            // remove page, pagintaion
            unset($param['page']);
            unset($param['pagination']);

            // manually set query operator based on key
            foreach ($param as $key => $value) {
                $cols = $key;

                $operator = in_array($key, ['nama', 'alamat', 'telp', 'target']) ? 'LIKE' : '=';
                $values = in_array($key, ['nama', 'alamat', 'telp', 'target']) ? '%' . $value . '%' : $value;

                $param[] = [$cols, $operator, $values];
                unset($param[$key]);
            }

            $getByParam = UnitQuery::getByParam($param, $pagination);

            self::$dataUnit = [];
            // reformat
            if ($pagination) {
                self::$dataUnit = $getByParam->getCollection()->map(function ($tag) {
                    return [
                        'unit_id' => $tag->id,
                        'kode' => $tag->kode,
                        'nama' => $tag->nama,
                        'alamat' => $tag->alamat,
                        'telp' => empty($tag->telp) ? '' : json_decode($tag->telp),
                        'fax' => empty($tag->fax) ? '' : json_decode($tag->fax),
                        'milik' => $tag->milik,
                        'target' => $tag->target,
                        'is_show_mobile' => $tag->is_show_mobile,
                        'latitude' => $tag->latitude,
                        'longitude' => $tag->longitude,
                        'status' => $tag->status,
                        'id_aol' => $tag->id_aol,
                        'regional' => [
                            'regional_id' => empty($tag->regional) ? null : $tag->regional->id,
                            'kode' => empty($tag->regional) ? null : $tag->regional->kode,
                            'nama' => empty($tag->regional) ? null : $tag->regional->nama
                        ],
                        'building' => [
                            'building_id' => empty($tag->building) ? null : $tag->building->id,
                            'nama' => empty($tag->building) ? null : $tag->building->nama,
                            'alamat' => empty($tag->building) ? null : $tag->building->alamat,
                            'pemilik' => empty($tag->building) ? null : $tag->building->pemilik,
                            'telp_pemilik' => empty($tag->building) ? null : json_decode($tag->building->telp_pemilik),
                            'npwp_pemilik' => empty($tag->building) ? null : $tag->building->npwp_pemilik,
                            'bank' => empty($tag->building) ? null : $tag->building->bank_id,
                            'rekening' => empty($tag->building) ? null : $tag->building->rekening,
                            'rekening_nama' => empty($tag->building) ? null : $tag->building->rekening_nama,
                            'pic' => empty($tag->building) ? null : $tag->building->pic,
                            'telp_pic' => empty($tag->building) ? null : json_decode($tag->building->telp_pic),
                            'building_status' => [
                                'status' => empty($tag->building->buildingStatus) ? null : $tag->building->buildingStatus->status,
                                'satuan_sewa' => empty($tag->building->buildingStatus) ? null : $tag->building->buildingStatus->satuan_sewa,
                                'harga_sewa' => empty($tag->building->buildingStatus) ? null : $tag->building->buildingStatus->harga_sewa,
                                'pembagi_hasil' => empty($tag->building->buildingStatus) ? null : $tag->building->buildingStatus->pembagi_hasil,
                                'minimum_charge' => empty($tag->building->buildingStatus) ? null : $tag->building->buildingStatus->minimum_charge,
                                'persentase_jasa' => empty($tag->building->buildingStatus) ? null : $tag->building->buildingStatus->persentase_jasa,
                                'persentase_dressing' => empty($tag->building->buildingStatus) ? null : $tag->building->buildingStatus->persentase_dressing,
                                'persentase_bhp' => empty($tag->building->buildingStatus) ? null : $tag->building->buildingStatus->persentase_bhp,
                                'pajak' => empty($tag->building->buildingStatus) ? null : $tag->building->buildingStatus->pajak
                            ]
                        ]
                    ];
                })->toArray();

                $transform = new LengthAwarePaginator(
                    self::$dataUnit,
                    $getByParam->total(),
                    $getByParam->perPage(),
                    $getByParam->currentPage(),
                    [
                        'path' => $request->fullUrl(),
                        'query' => [
                            'page' => $getByParam->currentPage()
                        ]
                    ]
                );
            } else {
                foreach ($getByParam as $unit) {
                    $transform[] = [
                        'unit_id' => $unit->id,
                        'kode' => $unit->kode,
                        'nama' => $unit->nama,
                        'status' => $unit->status,
                        'alamat' => $unit->alamat,
                        'telp' => empty($unit->telp) ? '' : json_decode($unit->telp),
                        'latitude' => $unit->latitude,
                        'longitude' => $unit->longitude,
                    ];
                }
            }

            self::$jsonData['data'] = $transform;

            return self::$jsonData;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        }
    }

    public static function getById(string $unitId)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        try {
            $getById = UnitQuery::getById($unitId);

            if (empty($getById)) {
                throw new \Exception('id Unit tidak ditemukan', 404);
            } else {
                self::$dataUnit = [
                    'unit_id' => $getById->id,
                    'kode' => $getById->kode,
                    'nama' => $getById->nama,
                    'alamat' => $getById->alamat,
                    'telp' => empty($getById->telp) ? '' : json_decode($getById->telp),
                    'fax' => empty($getById->fax) ? '' : json_decode($getById->fax),
                    'milik' => $getById->milik,
                    'target' => $getById->target,
                    'is_show_mobile' => $getById->is_show_mobile,
                    'latitude' => $getById->latitude,
                    'longitude' => $getById->longitude,
                    'status' => $getById->status,
                    'id_aol' => $getById->id_aol,
                    'regional' => [
                        'regional_id' => empty($getById->regional) ? '' : $getById->regional->id,
                        'kode' => empty($getById->regional) ? '' : $getById->regional->kode,
                        'nama' => empty($getById->regional) ? '' : $getById->regional->nama
                    ],
                    'building' => [
                        'building_id' => empty($getById->building) ? null : $getById->building->id,
                        'nama' => empty($getById->building) ? null : $getById->building->nama,
                        'alamat' => empty($getById->building) ? null : $getById->building->alamat,
                        'pemilik' => empty($getById->building) ? null : $getById->building->pemilik,
                        'telp_pemilik' => empty($getById->building) ? null : json_decode($getById->building->telp_pemilik),
                        'npwp_pemilik' => empty($getById->building) ? null : $getById->building->npwp_pemilik,
                        'bank_id' => empty($getById->building) ? null : $getById->building->bank_id,
                        'rekening' => empty($getById->building) ? null : $getById->building->rekening,
                        'rekening_nama' => empty($getById->building) ? null : $getById->building->rekening_nama,
                        'pic' => empty($getById->building) ? null : $getById->building->pic,
                        'telp_pic' => empty($getById->building) ? null : json_decode($getById->building->telp_pic),
                        'building_status' => [
                            'status' => empty($getById->building->buildingStatus) ? null : $getById->building->buildingStatus->status,
                            'harga_sewa' => empty($getById->building->buildingStatus) ? null : $getById->building->buildingStatus->harga_sewa,
                            'satuan_sewa' => empty($getById->building->buildingStatus) ? null : $getById->building->buildingStatus->satuan_sewa,
                            'pembagi_hasil' => empty($getById->building->buildingStatus) ? null : $getById->building->buildingStatus->pembagi_hasil,
                            'minimum_charge' => empty($getById->building->buildingStatus) ? null : $getById->building->buildingStatus->minimum_charge,
                            'persentase_jasa' => empty($getById->building->buildingStatus) ? null : $getById->building->buildingStatus->persentase_jasa,
                            'persentase_dressing' => empty($getById->building->buildingStatus) ? null : $getById->building->buildingStatus->persentase_dressing,
                            'persentase_bhp' => empty($getById->building->buildingStatus) ? null : $getById->building->buildingStatus->persentase_bhp,
                            'pajak' => empty($getById->building->buildingStatus) ? null : $getById->building->buildingStatus->pajak
                        ]
                    ],
                    'rute' => [
                        'rute_id' => empty($getById->rute) ? null : $getById->rute->id,
                        'nama' => empty($getById->rute) ? null : $getById->rute->nama
                    ]
                ];

                self::$jsonData['data'] = self::$dataUnit;
            }

            return self::$jsonData;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        }
    }

    public static function add(object $request, string $userId)
    {
        self::$jsonData['status'] = 201;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = 'Berhasil Menambah Unit';

        DB::beginTransaction();
        try {
            $request->merge(['telp' => json_encode($request->input('telp'))]);
            // add created by
            $request->add(['created_by' => $userId]);

            // get asmen_area_id by regional_id
            // then add asmen_area_id value to be inserted in Unit
            $asmenRegional = Regional::where('id', $request->input('regional_id'))->first();
            $request->add(['asmen_area_id' => $asmenRegional->asmen_area_id]);

            // get Aol auth internal
            $getAolAuthInternal = Http::get(env('SERVICE_AUTH') . '/api/auth/aol/all-auth-data');
            $resAolAuthInternal = $getAolAuthInternal->json();

            if ($getAolAuthInternal->failed()) {
                throw new \Exception($resAolAuthInternal['message'], 400);
            }

            $tokenAol = $resAolAuthInternal['data']['token'];
            $xSessionId = $resAolAuthInternal['data']['x-session-id'];
            $hostAol = $resAolAuthInternal['data']['host'];
            // insert aol
            $dataCreate = [
                'name' =>  $request->input('nama'),
                'customerNo' => $request->input('kode')
            ];
            $aolInsert = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenAol,
                'X-Session-ID' => $xSessionId
            ])
                ->post($hostAol . '/accurate/api/customer/save.do?' . http_build_query($dataCreate));
            $resAolInsert = $aolInsert->json();

            if ($aolInsert->failed()) {
                $paramError = [
                    'url' => $hostAol . '/accurate/api/customer/save.do',
                    'response' => $resAolInsert,
                    'scope' => 'customer_save',
                    'origin' => 'configuration insert new unit'
                ];
                $authLogAolError = Http::get(env('SERVICE_AUTH') . '/api/auth/aol/general-api/log-error?' . http_build_query($paramError));
            } else {
                $request->add(['id_aol' => $resAolInsert['r']['id']]);
            }

            $paramUnit = self::reMapUnit($request->all(), true);
            $saveUnit = UnitQuery::save($paramUnit);

            $paramBuilding = self::reMapBuilding($saveUnit->id, $request->all());
            $saveBuilding = BuildingQuery::save($paramBuilding);

            $paramBuildingStatus = self::reMapBuildingStatus($saveBuilding->id, $request->all());
            $saveBuildingStatus = BuildingStatusQuery::save($paramBuildingStatus);

            DB::commit();
            self::$jsonData['data'] = [
                'unit_id' => $saveUnit->id
            ];

            return response()->json(self::$jsonData, self::$jsonData['status']);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        } catch (\Exception $e) {
            DB::rollback();

            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        }
    }

    public static function edit(object $request, string $unitId, string $userId)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = 'Berhasil Merubah Unit';

        DB::beginTransaction();
        try {
            $request->merge(['telp' => json_encode($request->input('telp'))]);

            // add updated by
            $request->add(['updated_by' => $userId]);

            // get asmen_area_id by regional_id
            // then add asmen_area_id value to be inserted in Unit
            $asmenRegional = Regional::where('id', $request->input('regional_id'))->first();
            $request->add(['asmen_area_id' => $asmenRegional->asmen_area_id]);

            $paramUnit = self::reMapUnit($request->all(), false);
            $updateUnit = UnitQuery::update($unitId, $paramUnit);

            $paramBuilding = self::reMapBuilding($unitId, $request->all());
            $updateBuilding = BuildingQuery::updateColumn(['unit_id' => $unitId], $paramBuilding);

            // get building id
            $building = BuildingQuery::getByUnitId($unitId, false);

            $paramBuildingStatus = self::reMapBuildingStatus($building->id, $request->all());
            $updateBuildingStatus = BuildingStatusQuery::updateColumn(['building_id' => $building->id], $paramBuildingStatus);

            DB::commit();
            return response()->json(self::$jsonData, self::$jsonData['status']);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        } catch (\Exception $e) {
            DB::rollback();

            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        }
    }

    public static function sumtarget(string $regionalId)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        try {
            $sumUnit = Unit::where('regional_id', $regionalId)->sum('target');

            self::$jsonData['data'] = $sumUnit;

            return self::$jsonData;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        }
    }

    // private
    private static function reMapUnit($request, bool $isSave)
    {
        if ($isSave) {
            $paramUnit = [
                'asmen_area_id' => $request['asmen_area_id'],
                'regional_id' => $request['regional_id'],
                'kode' => $request['kode'],
                'nama' => $request['nama'],
                'alamat' => $request['alamat'],
                'telp' => $request['telp'],
                'fax' => $request['fax'],
                'milik' => $request['milik'],
                'target' => $request['target'],
                'is_show_mobile' => $request['is_show_mobile'],
                'latitude' => $request['latitude'],
                'longitude' => $request['longitude'],
                'status' => $request['status'],
                'created_by' => $request['created_by'],
                'id_aol' => $request['id_aol']
            ];
        } else {
            $paramUnit = [
                'asmen_area_id' => $request['asmen_area_id'],
                'regional_id' => $request['regional_id'],
                'kode' => $request['kode'],
                'nama' => $request['nama'],
                'alamat' => $request['alamat'],
                'telp' => $request['telp'],
                'fax' => $request['fax'],
                'milik' => $request['milik'],
                'target' => $request['target'],
                'is_show_mobile' => $request['is_show_mobile'],
                'latitude' => $request['latitude'],
                'longitude' => $request['longitude'],
                'status' => $request['status'],
                'updated_by' => $request['updated_by']
            ];
        }

        return $paramUnit;
    }

    private static function reMapBuilding($unitId, $request)
    {
        $paramBuilding = [
            'unit_id' => $unitId,
            'nama' => $request['building']['nama'],
            'alamat' => $request['alamat'],
            'pemilik' => $request['building']['pemilik'],
            'telp_pemilik' => json_encode($request['building']['telp_pemilik']),
            'npwp_pemilik' => $request['building']['npwp_pemilik'],
            'bank_id' => $request['building']['bank_id'],
            'rekening' => $request['building']['rekening'],
            'rekening_nama' => $request['building']['rekening_nama'],
            'pic' => $request['building']['pic'],
            'telp_pic' => json_encode($request['building']['telp_pic'])
        ];

        return $paramBuilding;
    }

    private static function reMapBuildingStatus($buildingId, $request)
    {
        $paramBuilding = [
            'building_id' => $buildingId,
            'status' => $request['building']['building_status']['status'],
            'harga_sewa' => $request['building']['building_status']['harga_sewa'],
            'satuan_sewa' => $request['building']['building_status']['satuan_sewa'],
            'pembagi_hasil' => $request['building']['building_status']['pembagi_hasil'],
            'minimum_charge' => $request['building']['building_status']['minimum_charge'],
            'persentase_jasa' => $request['building']['building_status']['persentase_jasa'],
            'persentase_dressing' => $request['building']['building_status']['persentase_dressing'],
            'persentase_bhp' => $request['building']['building_status']['persentase_bhp'],
            'pajak' => $request['building']['building_status']['pajak']
        ];

        return $paramBuilding;
    }
}
