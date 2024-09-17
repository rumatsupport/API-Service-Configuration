<?php

namespace App\Http\Controllers\V2\Bank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// modules
use App\Modules\BankModules;

// rules
use App\Rules\Bank\AddBank;
use App\Rules\Bank\EditBank;

class BankController extends Controller
{
    private static $jsonData, $myId, $roleId, $unitId, $token;

    public function __construct(Request $request)
    {
        self::$jsonData['status'] = 200;
        self::$jsonData['data'] = [];
        self::$jsonData['message'] = '';

        self::$token = \str_replace("Bearer ","",$request->header('Authorization'));
        self::$roleId = $request->scope->role_id;
        self::$unitId = $request->scope->unit_id;
        self::$myId = $request->scope->user_id;

        $request->request->remove('scope');
    }

    public function getAll(Request $request)
    {
        try {
            return BankModules::getAll($request);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function getById(string $bankId)
    {
        try {
            return BankModules::getById($bankId);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function add(AddBank $request)
    {
        try {
            return BankModules::add($request);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function edit(string $bankId, EditBank $request)
    {
        try {
            return BankModules::edit($request, $bankId);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }
}
