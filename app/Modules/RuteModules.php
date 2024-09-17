<?php

namespace App\Modules;

// vendor
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

// models
use App\Models\Rute;

// query
use App\Query\RuteQuery;
use App\Query\UnitQuery;

class RuteModules
{
    private static $jsonData, $responseRute, $dataRute;

    public static function getAll(object $request)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        try {
            $param = $request->query();

            $rute = new Rute();
            $column = $rute->getTableColumns();

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

                $operator = in_array($key, ['nama', 'unit']) ? 'LIKE' : '=';
                $values = in_array($key, ['nama', 'unit']) ? '%' . $value . '%' : $value;

                $param[] = [$cols, $operator, $values];
                unset($param[$key]);
            }

            $getByParam = RuteQuery::getByParam($param, $pagination);

            self::$dataRute = [];
            $unitList = [];
            // reformat
            if ($pagination) {
                self::$dataRute = $getByParam->getCollection()->map(function ($tag) use ($unitList) {
                    if (!empty($tag->unit)) {
                        $unitList = json_decode($tag->unit);

                        foreach ($unitList as $units) {
                            $thisUnit = UnitQuery::getById($units);
                            $untList[] = [
                                'unit_id' => $thisUnit->id,
                                'nama' => $thisUnit->nama
                            ];
                        }
                    }

                    return [
                        'rute_id' => $tag->id,
                        'nama' => $tag->nama,
                        'unit' => $untList
                    ];
                })->toArray();

                $transform = new LengthAwarePaginator(
                    self::$dataRute,
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
                foreach ($getByParam as $rute) {
                    if (!empty($rute->unit)) {
                        $unitList = json_decode($rute->unit);

                        foreach ($unitList as $units) {
                            $thisUnit = UnitQuery::getById($units);
                            $untList[] = [
                                'unit_id' => $thisUnit->id,
                                'nama' => $thisUnit->nama
                            ];
                        }
                    }
                    $transform[] = [
                        'rute_id' => $rute->id,
                        'nama' => $rute->nama,
                        'unit' => $untList
                    ];
                }
            }

            self::$jsonData['data'] = $transform;

            return self::$jsonData;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        }
    }

    public static function getById(string $ruteId)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        try {
            $getById = RuteQuery::getById($ruteId);

            if (empty($getById)) {
                throw new \Exception('id Rute tidak ditemukan', 404);
            } else {
                $unitList = json_decode($getById->unit);
                foreach ($unitList as $units) {
                    $thisUnit = UnitQuery::getById($units);
                    $untList[] = [
                        'unit_id' => $thisUnit->id,
                        'nama' => $thisUnit->nama
                    ];
                }
                self::$dataRute = [
                    'rute_id' => $getById->id,
                    'nama' => $getById->nama,
                    'unit' => $untList
                ];

                self::$jsonData['data'] = self::$dataRute;
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
        self::$jsonData['message'] = 'Berhasil Menambah Rute';

        DB::beginTransaction();
        try {

            $request->add(['created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
            $request->add(['created_by' => $userId]);

            $saveRute = RuteQuery::save($request->all());

            // set rute_id on every unit listed
            foreach ($request->input('unit') as $rute) {
                UnitQuery::updateColumn(['id' => $rute], ['rute_id' => $saveRute->id]);
            }

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

    public static function edit(string $ruteId, object $request, string $userId)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = 'Berhasil Merubah Rute';

        DB::beginTransaction();
        try {

            $getThisRute = RuteQuery::getById($ruteId);
            // remove unit with this rute
            foreach (json_decode($getThisRute->unit) as $rute) {
                UnitQuery::updateColumn(['id' => $rute], ['rute_id' => '']);
            }

            $request->add(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
            $request->add(['updated_by' => $userId]);

            $updateRute = RuteQuery::update($ruteId, $request->except(['rute_id']));

            // set rute_id on every unit listed
            foreach ($request->input('unit') as $rute) {
                UnitQuery::updateColumn(['id' => $rute], ['rute_id' => $ruteId]);
            }

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
