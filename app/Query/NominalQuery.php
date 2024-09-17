<?php

namespace App\Query;

// models
use App\Models\Nominal;

Class NominalQuery{
    public static function getByParam(array $columnValue, bool $pagination)
    {
        try {
            if ($pagination) {
                $nominal = Nominal::where($columnValue)->orderBy('nominal', 'ASC')->orderBy('tahun', 'ASC')->paginate(20);
            } else {
                $nominal = Nominal::where($columnValue)->orderBy('nominal', 'ASC')->orderBy('tahun', 'ASC')->get();
            }

            return $nominal;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function getById(string $nominalId)
    {
        try {
            $nominal = Nominal::where('id', $nominalId)->first();

            return $nominal;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function save(array $data)
    {
        try {
            $nominal = new Nominal();
            $nominal->id = $nominal->getUniqueID();
            $nominal->nominal = $data['nominal'];
            $nominal->tahun = $data['tahun'];
            $nominal->satuan = $data['satuan'];
            $nominal->status = $data['status'];
            $nominal->created_at = $data['created_at'];
            $nominal->created_by = $data['created_by'];
            $nominal->save();

            return $nominal;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function update(string $nominalId, array $data)
    {
        try {
            return Nominal::where('id', $nominalId)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function updateColumn(array $columnValue, array $data)
    {
        try {
            return Nominal::where($columnValue)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }
}
