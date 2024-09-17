<?php

namespace App\Http\Controllers\V2\Nominal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// modules
use App\Modules\NominalModules;

// rules
use App\Rules\Nominal\AddNominal;
use App\Rules\Nominal\EditNominal;

class NominalController extends Controller
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

    public function getAll(Request $request)
    {
        try {
            return NominalModules::getAll($request);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function getById(string $nominalId)
    {
        try {
            return NominalModules::getById($nominalId);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function add(AddNominal $request)
    {
        try {
            return NominalModules::add($request, self::$myId);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function edit(EditNominal $request, string $nominalId)
    {
        try {
            return NominalModules::edit($request, $nominalId, self::$myId);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }
}
