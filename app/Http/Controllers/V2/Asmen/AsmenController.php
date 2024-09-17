<?php

namespace App\Http\Controllers\V2\Asmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

// models
use App\Models\Asmen;

// rules
use App\Rules\Asmen\Add;
use App\Rules\Asmen\Edit;

class AsmenController extends Controller
{
    private static $jsonData, $myId, $roleId, $unitId, $token, $dataAsmen;

    public function __construct(Request $request)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        if (empty($request->header('Basic'))) {
            self::$token = \str_replace("Bearer ", "", $request->header('Authorization'));
            self::$roleId = $request->scope->role_id;
            self::$unitId = $request->scope->unit_id;
            self::$myId = $request->scope->user_id;

            $request->request->remove('scope');
        }
    }

    public function getAll(Request $request)
    {
        try {
            $param = $request->query();
            $transform = [];

            $asmen = new Asmen();
            $column = $asmen->getTableColumns();

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

            if ($pagination) {
                $asmenData = Asmen::paginate(20);
                self::$dataAsmen = $asmenData->getCollection()->map(function ($tag) {
                    return [
                        'asmen_area_id' => $tag->id,
                        'kode' => $tag->kode,
                        'nama' => $tag->nama
                    ];
                })->toArray();

                $transform = new LengthAwarePaginator(
                    self::$dataAsmen,
                    $asmenData->total(),
                    $asmenData->perPage(),
                    $asmenData->currentPage(), [
                        'path' => $request->fullUrl(),
                        'query' => [
                            'page' => $asmenData->currentPage(),
                        ],
                    ]
                );
            } else {
                $asmenData = Asmen::get();
                foreach ($asmenData as $asmenArea) {
                    $transform[] = [
                        'asmen_area_id' => $asmenArea->id,
                        'kode' => $asmenArea->kode,
                        'nama' => $asmenArea->nama
                    ];
                }
            }

            self::$jsonData['data'] = $transform;

            return self::$jsonData;
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function getById(string $asmenAreaId)
    {
        try {
            $getById = Asmen::where('id',$asmenAreaId)->first();

            if (empty($getById)) {
                self::$jsonData['status'] = 404;
                self::$jsonData['data'] = [];
                self::$jsonData['message'] = 'id Area Asmen tidak ditemukan';
            } else {
                self::$dataAsmen = [
                    'asmen_area_id' => $getById->id,
                    'kode' => $getById->kode,
                    'nama' => $getById->nama
                ];

                self::$jsonData['data'] = self::$dataAsmen;
            }

            return self::$jsonData;
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function add(Add $request)
    {
        DB::beginTransaction();
        try {
            $asmen = new Asmen();
            $asmen->id = $asmen->getUniqueID();
            $asmen->kode = $request->input('kode');
            $asmen->nama = $request->input('nama');
            $asmen->save();

            DB::commit();
            self::$jsonData['status'] = 201;
            self::$jsonData['data'] = [
                'asmen_area_id' => $asmen->id,
            ];
            self::$jsonData['message'] = 'Berhasil Menambah Area Asmen';

            return self::$jsonData;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            self::$jsonData['status']   = 500;
            self::$jsonData['message']  = $e->getMessage();
        } catch (\Exception $e) {
            DB::rollBack();

            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function edit(Edit $request, string $asmenAreaId)
    {
        DB::beginTransaction();
        try {
            Asmen::where('id', $asmenAreaId)->update($request->except(['asmen_area_id']));
            DB::commit();
            self::$jsonData['status'] = 200;
            self::$jsonData['message'] = 'Berhasil Merubah Area Asmen';

            return self::$jsonData;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            self::$jsonData['status']   = 500;
            self::$jsonData['message']  = $e->getMessage();
        } catch (\Exception $e) {
            DB::rollBack();

            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }
}
