<?php

namespace App\Http\Controllers\V2\Aol;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\Unit;

class AolController extends Controller
{
    public function bulkIntegrate()
    {
        DB::beginTransaction();
        try {
            // get Aol auth internal
            $getAolAuthInternal = Http::get(env('SERVICE_AUTH') . '/api/auth/aol/all-auth-data');
            $resAolAuthInternal = $getAolAuthInternal->json();

            if ($getAolAuthInternal->failed()) {
                throw new \Exception($resAolAuthInternal['message'], 400);
            }

            $token = $resAolAuthInternal['data']['token'];
            $xSessionId = $resAolAuthInternal['data']['x-session-id'];
            $host = $resAolAuthInternal['data']['host'];

            $unit = new Unit();
            $unitList = $unit::limit(10000)->get()->toArray();
            $totalUnit = $unit::count();
            $failed = 0;
            $success = 0;
            $existed = 0;
            $failedData = '';
            $successData = '';

            foreach ($unitList as $keyUnit => $vUnit) {
                $paramList = [
                    'fields' => 'id,customerNo,name',
                    'filter.keywords.op' => 'EQUAL',
                    'filter.keywords' => $unitList[$keyUnit]['kode']
                ];
                $getAolUnitList = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'X-Session-ID' => $xSessionId
                ])
                    ->get($host . '/accurate/api/customer/list.do', http_build_query($paramList));
                $resAolUnitList = $getAolUnitList->json();

                if ($getAolUnitList->failed()) {
                    $failed = $totalUnit;
                    $paramError = [
                        'url' => $host . '/accurate/api/customer/list.do',
                        'response' => $resAolUnitList,
                        'scope' => 'customer_list',
                        'origin' => 'configuration'
                    ];
                    $authLogAolError = Http::get(env('SERVICE_AUTH') . '/api/auth/aol/general-api/log-error?' . http_build_query($paramError));
                    continue;
                }
                $aolUnitData = $resAolUnitList['d'];
                if (empty($aolUnitData)) {
                    $dataCreate = [
                        'name' =>  $unitList[$keyUnit]['nama'],
                        'customerNo' => $unitList[$keyUnit]['kode']
                    ];
                    $aolInsert = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token,
                        'X-Session-ID' => $xSessionId
                    ])
                        ->post($host . '/accurate/api/customer/save.do?' . http_build_query($dataCreate));
                    $resAolInsert = $aolInsert->json();

                    if ($aolInsert->failed()) {
                        $paramError = [
                            'url' => $host . '/accurate/api/customer/save.do',
                            'response' => $resAolInsert,
                            'scope' => 'customer_save',
                            'origin' => 'configuration'
                        ];
                        $authLogAolError = Http::get(env('SERVICE_AUTH') . '/api/auth/aol/general-api/log-error?' . http_build_query($paramError));
                        $failed = $failed + 1;
                    } else {
                        $unit::where('id', $unitList[$keyUnit]['id'])->update(['id_aol' => $resAolInsert['r']['id']]);
                        $success = $success + 1;
                    }
                } else {
                    if (empty($unitList[$keyUnit]['id_aol'])) {
                        $unit::where('id', $unitList[$keyUnit]['id'])->update(['id_aol' => $aolUnitData[0]['id']]);
                        $success = $success + 1;
                    } else {
                        $existed = $existed + 1;
                    }
                }
            }

            $jsonData = [
                'status' => '200',
                'data' => [
                    'total_data' => count($unitList),
                    'total_failed' => $failed,
                    'total_success' => $success,
                    'total_existed' => $existed
                ],
                'message' => 'Success'
            ];
            DB::commit();

            return response()->json($jsonData, $jsonData['status']);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            $status   = $e->getCode() ? $e->getCode() : 500;
            $message  = $e->getMessage();

            $jsonData = [
                'status' => $status,
                'data' => null,
                'message' => $message
            ];

            return response()->json($jsonData, $status);
        } catch (\Exception $e) {
            DB::rollback();
            $status   = $e->getCode() ? $e->getCode() : 500;
            $message  = $e->getMessage();

            $jsonData = [
                'status' => $status,
                'data' => null,
                'message' => $message
            ];

            return response()->json($jsonData, $status);
        }
    }
}
