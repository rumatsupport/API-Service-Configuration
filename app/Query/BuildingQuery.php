<?php

namespace App\Query;

// models
use App\Models\Building;

Class BuildingQuery{
    public static function getByParam(array $columnValue, bool $pagination)
    {
        try {
            if ($pagination) {
                $Building = Building::where($columnValue)->paginate(20);
            } else {
                $Building = Building::where($columnValue)->get();
            }

            return $Building;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function getById(string $BuildingId)
    {
        try {
            $Building = Building::where('id', $BuildingId)->first();

            return $Building;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function getByUnitId(string $UnitId)
    {
        try {
            $Building = Building::where('unit_id', $UnitId)->first();

            return $Building;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function save(array $data)
    {
        try {
            $Building = new Building();
            $Building->id = $Building->getUniqueID();
            $Building->unit_id = $data['unit_id'];
            $Building->nama = $data['nama'];
            $Building->alamat = $data['alamat'];
            $Building->pemilik = $data['pemilik'];
            $Building->telp_pemilik = $data['telp_pemilik'];
            $Building->npwp_pemilik = $data['npwp_pemilik'];
            $Building->rekening = $data['rekening'];
            $Building->rekening_nama = $data['rekening_nama'];
            $Building->pic = $data['pic'];
            $Building->telp_pic = $data['telp_pic'];
            $Building->save();

            return $Building;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function update(string $BuildingId, array $data)
    {
        try {
            return Building::where('id', $BuildingId)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function updateColumn(array $columnValue, array $data)
    {
        try {
            return Building::where($columnValue)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }
}
