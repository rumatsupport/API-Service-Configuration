<?php

namespace App\Modules;

// vendor
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

// models
use App\Models\Bank;

// query
use App\Query\BankQuery;

class BankModules
{
    private static $jsonData, $responseBank, $dataBank;

    public static function getAll(object $request)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        try {
            $param = $request->query();

            $bank = new Bank();
            $column = $bank->getTableColumns();

            $column = array_diff($column, ['id']);
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

                $operator = in_array($key, ['nama', 'kode', 'kode_num']) ? 'LIKE' : '=';
                $values = in_array($key, ['nama', 'kode', 'kode_num']) ? '%' . $value . '%' : $value;

                $param[] = [$cols, $operator, $values];
                unset($param[$key]);
            }

            $getByParam = BankQuery::getByParam($param, $pagination);

            self::$dataBank = [];
            // reformat
            if ($pagination) {
                self::$dataBank = $getByParam->getCollection()->map(function ($tag) {
                    return [
                        'bank_id' => $tag->id,
                        'nama' => $tag->nama,
                        'kode' => $tag->kode,
                        'kode_num' => $tag->kode_num
                    ];
                })->toArray();

                $transform = new LengthAwarePaginator(
                    self::$dataBank,
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
                foreach ($getByParam as $bank) {
                    $transform[] = [
                        'bank_id' => $bank->id,
                        'nama' => $bank->nama,
                        'kode' => $bank->kode,
                        'kode_num' => $bank->kode_num
                    ];
                }
            }

            self::$jsonData['data'] = $transform;

            return self::$jsonData;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
        }
    }

    public static function getById(string $bankId)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        try {
            $getById = BankQuery::getById($bankId);

            if (empty($getById)) {
                self::$jsonData['status'] = 404;
                self::$jsonData['data'] = [];
                self::$jsonData['message'] = 'id Bank tidak ditemukan';
            } else {
                self::$dataBank = [
                    'bank_id' => $getById->id,
                    'nama' => $getById->nama,
                    'kode' => $getById->kode,
                    'kode_num' => $getById->kode_num
                ];

                self::$jsonData['data'] = self::$dataBank;
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
        self::$jsonData['message'] = 'Berhasil Menambah Bank';

        DB::beginTransaction();
        try {
            $saveBank = BankQuery::save($request->all());

            DB::commit();
            self::$jsonData['data'] = [
                'bank_id' => $saveBank->id
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

    public static function edit(object $request, string $bankId)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = 'Berhasil Merubah Bank';

        DB::beginTransaction();
        try {
            $saveBank = BankQuery::update($bankId, $request->except(['bank_id']));

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
