<?php

namespace App\Modules;

// vendor
use App\Models\Regional;
use App\Models\Unit;
use App\Query\RegionalQuery;

// models
use Illuminate\Pagination\LengthAwarePaginator;

// query
use Illuminate\Support\Facades\DB;

class RegionalModules
{
    private static $jsonData, $responseRegional, $dataRegional;

    public static function getAll(object $request)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        try {
            $param = $request->query();

            $regional = new Regional();
            $column = $regional->getTableColumns();

            $column = array_diff($column, ['id']);
            $column = array_merge($column, ['page', 'pagination', 'group_alamat']);

            // return false if query param not in list
            foreach ($param as $key => $value) {
                if (!in_array($key, $column)) {
                    throw new \Exception('Unknown ' . $key . ' in query param list', 400);
                }
            }

            $pagination = isset($param['pagination']) && $param['pagination'] != null ? $param['pagination'] : true;
            $group_alamat = isset($param['group_alamat']) && $param['group_alamat'] != null ? $param['group_alamat'] : false;
            // remove page, pagintaion
            unset($param['page']);
            unset($param['pagination']);
            unset($param['group_alamat']);

            // manually set query operator based on key
            foreach ($param as $key => $value) {
                $cols = $key;

                $operator = in_array($key, ['nama', 'alamat', 'telp', 'kode', 'email']) ? 'LIKE' : '=';
                $values = in_array($key, ['nama', 'alamat', 'telp', 'kode', 'email']) ? '%' . $value . '%' : $value;

                $param[] = [$cols, $operator, $values];
                unset($param[$key]);
            }

            $getByParam = RegionalQuery::getByParam($param, $pagination);

            self::$dataRegional = [];
            // reformat
            if ($pagination) {
                self::$dataRegional = $getByParam->getCollection()->map(function ($tag) {
                    return [
                        'regional_id' => $tag->id,
                        'kode' => $tag->kode,
                        'nama' => $tag->nama,
                        'alamat' => $tag->alamat,
                        'telp' => empty($tag->telp) ? '' : json_decode($tag->telp),
                        'email' => $tag->email,
                        'status' => $tag->status,
                        'asmen_area' => $tag->asmen,
                    ];
                })->toArray();

                $transform = new LengthAwarePaginator(
                    self::$dataRegional,
                    $getByParam->total(),
                    $getByParam->perPage(),
                    $getByParam->currentPage(), [
                        'path' => $request->fullUrl(),
                        'query' => [
                            'page' => $getByParam->currentPage(),
                        ],
                    ]
                );
            } else {
                foreach ($getByParam as $regional) {
                    $transform[] = [
                        'regional_id' => $regional->id,
                        'kode' => $regional->kode,
                        'nama' => $regional->nama,
                        'alamat' => $regional->alamat,
                        'telp' => empty($regional->telp) ? '' : json_decode($regional->telp),
                        'email' => $regional->email,
                        'status' => $regional->status,
                        'unit' => $regional->unit->toArray(), // call toArray() to make it as an array instead of datbase collection
                    ];
                }
                if ($group_alamat) {
                    // GROUP BY KEY NAME alamat
                    // that has similiar value up to 70%
                    $febri = [];
                    // Iterate through the data array
                    foreach ($transform as $item) {
                        $alamat = $item['alamat'];

                        // Initialize the group key as null
                        $groupKey = null;

                        // Iterate through the groupedData to find a key that matches the current "alamat"
                        foreach ($febri as $key => $groupedItems) {
                            similar_text($alamat, $key, $similarityPercentage);

                            // Define a threshold for similarity (adjust as needed)
                            $similarityThreshold = 70; // 70% similarity, for example

                            if ($similarityPercentage >= $similarityThreshold) {
                                $groupKey = $key;
                                break; // Stop checking further, we found a match
                            }
                        }

                        // If no matching group key was found, create a new group
                        if ($groupKey === null) {
                            $groupKey = $alamat;
                        }

                        // Add the current item to the group
                        $febri[$groupKey][] = $item;
                    }
                    $transform = array_values($febri);
                    // GROUP THE UNIT BASED ON EACH ARRAY
                    // KEEP ONLY FIRST KEY GROUP BY ARRAY
                    $combinedFebri = [];
                    foreach (array_values($febri) as $sub) {
                        if (!empty($sub)) {
                            $dataFirst = $sub[0];

                            $unitCombined = [];
                            foreach ($sub as $isi) {
                                if (!empty($isi['unit'])) {
                                    $unitCombined = array_merge($unitCombined,$isi['unit']);
                                }
                            }

                            $dataFirst['unit'] = $unitCombined;

                            $combinedFebri[] = $dataFirst;
                        }
                    }

                    $transform = $combinedFebri;
                }
            }

            self::$jsonData['data'] = $transform;

            return self::$jsonData;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        }
    }

    public static function getById(string $regionalId)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        try {
            $getById = RegionalQuery::getById($regionalId);

            if (empty($getById)) {
                self::$jsonData['status'] = 404;
                self::$jsonData['data'] = [];
                self::$jsonData['message'] = 'id Regional tidak ditemukan';
            } else {
                self::$dataRegional = [
                    'regional_id' => $getById->id,
                    'kode' => $getById->kode,
                    'nama' => $getById->nama,
                    'alamat' => $getById->alamat,
                    'telp' => empty($getById->telp) ? '' : json_decode($getById->telp),
                    'email' => $getById->email,
                    'status' => $getById->status,
                    'asmen_area_id' => $getById->asmen_area_id,
                ];

                self::$jsonData['data'] = self::$dataRegional;
            }

            return self::$jsonData;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        }
    }

    public static function add(object $request)
    {
        self::$jsonData['status'] = 201;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = 'Berhasil Menambah Regional';

        DB::beginTransaction();
        try {
            $request->merge(['telp' => json_encode($request->input('telp'))]);

            $saveRegional = RegionalQuery::save($request->all());

            DB::commit();
            self::$jsonData['data'] = [
                'regional_id' => $saveRegional->id,
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

    public static function edit(object $request, string $regionalId)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = 'Berhasil Merubah Regional';

        DB::beginTransaction();
        try {
            $request->merge(['telp' => json_encode($request->input('telp'))]);

            // update all unit with this regional
            // add asmen_area_id
            Unit::where('regional_id', $regionalId)->update(['asmen_area_id' => $request->input('asmen_area_id')]);

            $saveRegional = RegionalQuery::update($regionalId, $request->except(['regional_id']));

            Unit::where('regional_id', $regionalId)->update(['asmen_area_id' => $request->input('asmen_area_id')]);
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
}
