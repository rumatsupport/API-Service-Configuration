<?php

namespace App\Query;

// models
use App\Models\Unit;

Class UnitQuery{
    public static function getByParam(array $columnValue, bool $pagination)
    {
        try {
            if ($pagination) {
                $unit = Unit::where($columnValue)->orderBy('nama', 'ASC')->paginate(20);
            } else {
                $unit = Unit::where($columnValue)->orderBy('nama', 'ASC')->get();
            }

            return $unit;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function getById(string $unitId)
    {
        try {
            $unit = Unit::where('id', $unitId)->first();

            return $unit;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function save(array $data)
    {
        try {
            $unit = new Unit();
            $unit->id = $unit->getUniqueID();
            $unit->asmen_area_id = $data['asmen_area_id'];
            $unit->regional_id = $data['regional_id'];
            $unit->kode = $data['kode'];
            $unit->nama = $data['nama'];
            $unit->alamat = $data['alamat'];
            $unit->telp = $data['telp'];
            $unit->fax = $data['fax'];
            $unit->milik = $data['milik'];
            $unit->target = $data['target'];
            $unit->status = $data['status'];
            $unit->is_show_mobile = $data['is_show_mobile'];
            $unit->latitude = $data['latitude'];
            $unit->longitude = $data['longitude'];
            $unit->created_by = $data['created_by'];
            $unit->save();

            return $unit;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function update(string $unitId, array $data)
    {
        try {
            return Unit::where('id', $unitId)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function updateColumn(array $columnValue, array $data)
    {
        try {
            return Unit::where($columnValue)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }
}
