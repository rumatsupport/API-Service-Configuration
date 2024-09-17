<?php

namespace App\Query;

// models
use App\Models\BuildingStatus;

Class BuildingStatusQuery{
    public static function getByParam(array $columnValue, bool $pagination)
    {
        try {
            if ($pagination) {
                $BuildingStatus = BuildingStatus::where($columnValue)->paginate(20);
            } else {
                $BuildingStatus = BuildingStatus::where($columnValue)->get();
            }

            return $BuildingStatus;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function getById(string $BuildingStatusId)
    {
        try {
            $BuildingStatus = BuildingStatus::where('id', $BuildingStatusId)->first();

            return $BuildingStatus;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function save(array $data)
    {
        try {
            $BuildingStatus = new BuildingStatus();
            $BuildingStatus->building_id = $data['building_id'];
            $BuildingStatus->status = $data['status'];
            $BuildingStatus->harga_sewa = $data['harga_sewa'];
            $BuildingStatus->satuan_sewa = $data['satuan_sewa'];
            $BuildingStatus->pembagi_hasil = $data['pembagi_hasil'];
            $BuildingStatus->minimum_charge = $data['minimum_charge'];
            $BuildingStatus->persentase_jasa = $data['persentase_jasa'];
            $BuildingStatus->persentase_dressing = $data['persentase_dressing'];
            $BuildingStatus->persentase_bhp = $data['persentase_bhp'];
            $BuildingStatus->pajak = $data['pajak'];
            $BuildingStatus->save();

            return $BuildingStatus;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function update(string $BuildingStatusId, array $data)
    {
        try {
            return BuildingStatus::where('id', $BuildingStatusId)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function updateColumn(array $columnValue, array $data)
    {
        try {
            return BuildingStatus::where($columnValue)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }
}
