<?php

namespace App\Helpers;

// vendor
use Illuminate\Support\Facades\DB;

Class Helpers{

    public static function generateUniqueID(array $configs)
    {
        $lastID = DB::table($configs['table'])->select(DB::raw('MAX(id) as last_id'))->first();
        if (!empty($lastID->last_id)) {
            $lastNumber = explode($configs['prefix'],$lastID->last_id);
            $newNumber = str_pad($lastNumber[1]+1, $configs['length'], '0', STR_PAD_LEFT);
        } else {
            $newNumber = str_pad('1', $configs['length'], '0', STR_PAD_LEFT);
        }
        $newId = $configs['prefix'].$newNumber;
        return $newId;
    }

    public static function liburNasional($tanggal)
    {
        $return = [];
        $r = file_get_contents("https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/holidays.json");
        $data = json_decode($r, true);

        if (isset($data[$tanggal]['summary'])) {
            $return = [
                'summary' => $data[$tanggal]['summary']
            ];
        }

        return $return;
    }
}
