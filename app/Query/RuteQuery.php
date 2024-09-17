<?php

namespace App\Query;

// models
use App\Models\Rute;

Class RuteQuery{
    public static function getByParam(array $columnValue, bool $pagination)
    {
        try {
            if ($pagination) {
                $Rute = Rute::where($columnValue)->paginate(20);
            } else {
                $Rute = Rute::where($columnValue)->get();
            }

            return $Rute;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function getById(string $RuteId)
    {
        try {
            $Rute = Rute::where('id', $RuteId)->first();

            return $Rute;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function save(array $data)
    {
        try {
            $Rute = new Rute();
            $Rute->id = $Rute->getUniqueID();
            $Rute->nama = $data['nama'];
            $Rute->unit = json_encode($data['unit']);
            $Rute->created_at = $data['created_at'];
            $Rute->created_by = $data['created_by'];
            $Rute->save();

            return $Rute;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function update(string $RuteId, array $data)
    {
        try {
            return Rute::where('id', $RuteId)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function updateColumn(array $columnValue, array $data)
    {
        try {
            return Rute::where($columnValue)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }
}
