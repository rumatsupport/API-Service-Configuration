<?php

namespace App\Http\Controllers\V2\DateInputLimit;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DateInputLimitController extends Controller
{
    private static $jsonData, $myId, $roleId, $unitId, $token;

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

    public function index(Request $request)
    {
        try {
            $tanggalAwalMinggu = false;
            $tanggalAwalNasional = false;

            $tanggalAkhirMinggu = false;
            $tanggalAkhirNasional = false;

            // set tanggal awal dan akhir
            $tanggalAwal = date("Y-m-t");
            $tanggalAkhir = date("Y-m-01", strtotime("+1 months"));

            // inputan bulan
            $bulanInput = $request->query('bulan');
            if (isset($bulanInput) && !empty($bulanInput)) {
                if ($bulanInput > 0 && $bulanInput < 13) {
                    $tanggalAwal = date("Y-m-t", strtotime(date("Y") . "-" . $bulanInput . "-" . date("d")));

                    $tanggalAkhir = date("Y-m-01", strtotime(date("Y") . "-" . $bulanInput . "-01"));
                    $tanggalAkhir = date("Y-m-01", strtotime($tanggalAkhir . "+1 months"));
                } else {
                    throw new \Exception("Minimal Bulan 1 & Maksimal Bulan 12", 400);
                }
            }

            // inputan tanggal awal & tanggal akhir
            $tanggalAwalInput = $request->query('tgl_awal');
            $tanggalAkhirInput = $request->query('tgl_akhir');
            // cek tgl_awal valid
            if (isset($tanggalAwalInput) && !empty($tanggalAwalInput)) {
                if (!date_create($tanggalAwalInput)) {
                    throw new \Exception("tgl_awal harus berupa tanggal dengan format Y-m-d", 400);
                }
                if (!isset($tanggalAkhirInput) || empty($tanggalAkhirInput)) {
                    throw new \Exception("tgl_akhir Harus Ada", 400);
                }
                $tanggalAwal = date_format(date_create($tanggalAwalInput), "Y-m-d");
            }
            // cek tgl_akhir valid dan lebih besar dari tgl_awal
            if (isset($tanggalAkhirInput) && !empty($tanggalAkhirInput)) {
                if (!date_create($tanggalAkhirInput)) {
                    throw new \Exception("tgl_akhir harus berupa tanggal dengan format Y-m-d", 400);
                }
                if (!isset($tanggalAwalInput) || empty($tanggalAwalInput)) {
                    throw new \Exception("tgl_awal Harus Ada", 400);
                }
                $tanggalAkhir = date_format(date_create($tanggalAkhirInput), "Y-m-d");
                if ($tanggalAkhir < $tanggalAwal) {
                    throw new \Exception("tgl_akhir harus lebih besar dari tgl_awal", 400);
                }
            }

            // cek jika tanggal awal adalah minggu
            if (date_format(date_create($tanggalAwal), "D") === "Sun") {
                $tanggalAwalMinggu = true;
            }
            // cek jika tanggal awal adalah libur nasional dan bukan minggu
            if (!empty(Helpers::liburNasional($tanggalAwal)) && $tanggalAwalMinggu === false) {
                $tanggalAwalNasional = true;
            }
            // +1 hari jika tanggal awal adalah minggu/libur nasional
            if ($tanggalAwalMinggu === true || $tanggalAwalNasional === true) {
                // optional
                // $tanggalAwal = date("Y-m-d", strtotime($tanggalAwal . "-1 days"));
                // tambah 1 hari untuk tanggal akhir
                $tanggalAkhir = date("Y-m-d", strtotime($tanggalAkhir . "+1 days"));
            }

            // cek jika tanggal akhir adalah minggu
            if (date_format(date_create($tanggalAkhir), "D") === "Sun") {
                $tanggalAkhirMinggu = true;
            }
            // cek jika tanggal akhir adalah libur nasional dan bukan minggu
            if (!empty(Helpers::liburNasional($tanggalAkhir)) && $tanggalAkhirMinggu === false) {
                $tanggalAkhirNasional = true;
            }
            // +1 hari jika tanggal akhir adalah minggu/libur nasional
            if ($tanggalAkhirMinggu === true || $tanggalAkhirNasional === true) {
                $tanggalAkhir = date("Y-m-d", strtotime($tanggalAkhir . "+1 days"));
            }

            // periode
            $periode = new \DatePeriod(
                date_create($tanggalAwal),
                new \DateInterval("P1D"),
                date_create($tanggalAkhir)->add(new \DateInterval("P1D"))
            );
            $periode = iterator_to_array($periode);
            $ranges = [];
            foreach ($periode as $value) {
                // hilangkan libur nasional
                if (!empty(Helpers::liburNasional($value->format("Y-m-d")))) {
                    continue;
                }
                // hilangkan minggu
                if ($value->format("D") === "Sun") {
                    continue;
                }
                $ranges[] = $value->format("Y-m-d");
            }

            self::$jsonData['data'] = $ranges;
        } catch (\Exception $e) {
            self::$jsonData['status'] = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['data'] = [];
            self::$jsonData['message'] = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    private static function hasilAkhirBulan($date)
    {
        try {
            //code...
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 400);

        }
    }
}
