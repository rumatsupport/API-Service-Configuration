<?php

namespace App\Query;

// models
use App\Models\Bank;

Class BankQuery{
    public static function getByParam(array $columnValue, bool $pagination)
    {
        try {
            if ($pagination) {
                $bank = Bank::where($columnValue)->paginate(20);
            } else {
                $bank = Bank::where($columnValue)->get();
            }

            return $bank;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function getById(string $bankId)
    {
        try {
            $bank = Bank::where('id', $bankId)->first();

            return $bank;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function save(array $data)
    {
        try {
            $bank = new Bank();
            $bank->id = $bank->getUniqueID();
            $bank->nama = $data['nama'];
            $bank->kode = $data['kode'];
            $bank->kode_num = $data['kode_num'];
            $bank->save();

            return $bank;
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function update(string $bankId, array $data)
    {
        try {
            return Bank::where('id', $bankId)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }

    public static function updateColumn(array $columnValue, array $data)
    {
        try {
            return Bank::where($columnValue)->update($data);
        } catch(\Illuminate\Database\QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }
}
