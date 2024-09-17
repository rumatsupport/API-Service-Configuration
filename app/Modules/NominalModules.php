<?php

namespace App\Modules;

// vendor
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

// models
use App\Models\Nominal;

// query
use App\Query\NominalQuery;

Class NominalModules{
    private static $jsonData, $responseNominal, $dataNominal;

    public static function getAll(object $request)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        try {
            $param = $request->query();

            $nominal = new Nominal();
            $column = $nominal->getTableColumns();

            $column = array_diff($column, ['id']);
            $column = array_merge($column, ['page','pagination']);

            // return false if query param not in list
            foreach ($param as $key => $value) {
                if (!in_array($key,$column)) {
                    throw new \Exception('Unknown '.$key.' in query param list', 400);
                }
            }

            $pagination = isset($param['pagination']) && $param['pagination'] != null ? $param['pagination'] : true;
            // remove page, pagintaion
            unset($param['page']);
            unset($param['pagination']);

            // manually set query operator based on key
            foreach ($param as $key => $value) {
                $cols = $key;

                $operator = in_array($key,['nominal']) ? 'LIKE' : '=';
                $values = in_array($key,['nominal']) ? '%'.$value.'%' : $value;

                $param[] = [$cols, $operator, $values];
                unset($param[$key]);
            }

            $getByParam = NominalQuery::getByParam($param, $pagination);

            self::$dataNominal = [];
            // reformat
            if ($pagination) {
                self::$dataNominal = $getByParam->getCollection()->map(function ($tag) {
                    return [
                        'nominal_id' => $tag->id,
                        'nominal' => $tag->nominal,
                        'tahun' => $tag->tahun,
                        'satuan' => $tag->satuan,
                        'status' => $tag->status
                    ];
                })->toArray();

                $transform = new LengthAwarePaginator(
                    self::$dataNominal,
                    $getByParam->total(),
                    $getByParam->perPage(),
                    $getByParam->currentPage(), [
                        'path' => $request->fullUrl(),
                        'query' => [
                            'page' => $getByParam->currentPage()
                        ]
                    ]
                );
            } else {
                foreach ($getByParam as $nominal) {
                    $transform[] = [
                        'nominal_id' => $nominal->id,
                        'nominal' => $nominal->nominal,
                        'tahun' => $nominal->tahun,
                        'satuan' => $nominal->satuan,
                    ];
                }
            }

            self::$jsonData['data'] = $transform;

            return self::$jsonData;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        }
    }

    public static function getById(string $nominalId)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        try {
            $getById = NominalQuery::getById($nominalId);

            if (empty($getById)) {
                self::$jsonData['status'] = 404;
                self::$jsonData['data'] = [];
                self::$jsonData['message'] = 'id Nominal tidak ditemukan';
            } else {
                self::$dataNominal = [
                    'nominal_id' => $getById->id,
                    'nominal' => $getById->nominal,
                    'tahun' => $getById->tahun,
                    'satuan' => $getById->satuan,
                    'status' => $getById->status
                ];

                self::$jsonData['data'] = self::$dataNominal;
            }

            return self::$jsonData;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        }
    }

    public static function add(object $request, string $myId)
    {
        self::$jsonData['status'] = 201;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = 'Berhasil Menambah Nominal';

        DB::beginTransaction();
        try {
            $request->add(['tahun' => date('Y')]);
            $request->add(['status' => '1']);
            $request->add(['created_by' => $myId]);
            $request->add(['created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
            $saveNominal = NominalQuery::save($request->all());

            DB::commit();
            self::$jsonData['data'] = [
                'nominal_id' => $saveNominal->id
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

    public static function edit(object $request, string $nominalId)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = 'Berhasil Merubah Nominal';

        DB::beginTransaction();
        try {
            $editNominal = NominalQuery::update($nominalId, $request->except(['nominal_id']));

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
