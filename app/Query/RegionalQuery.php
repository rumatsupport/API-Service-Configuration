<?php

namespace App\Query;

// models
use App\Models\Regional;

Class RegionalQuery{
    public static function getByParam(array $columnValue, bool $pagination)
    {
        try {
            if ($pagination) {
                $regional = Regional::where($columnValue)->paginate(20);
            } else {
                $regional = Regional::where($columnValue)->get();
            }

            return $regional;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function getById(string $regionalId)
    {
        try {
            $regional = Regional::with(['asmen'])->where('id', $regionalId)->first();

            return $regional;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function save(array $data)
    {
        try {
            $regional = new Regional();
            $regional->id = $regional->getUniqueID();
            $regional->kode = $data['kode'];
            $regional->nama = $data['nama'];
            $regional->alamat = $data['alamat'];
            $regional->telp = $data['telp'];
            $regional->email = $data['email'];
            $regional->status = $data['status'];
            $regional->asmen_area_id = $data['asmen_area_id'];
            $regional->save();

            return $regional;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function update(string $regionalId, array $data)
    {
        try {
            return Regional::where('id', $regionalId)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function updateColumn(array $columnValue, array $data)
    {
        try {
            return Regional::where($columnValue)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }
}
