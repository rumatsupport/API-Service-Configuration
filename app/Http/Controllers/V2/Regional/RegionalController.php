<?php

namespace App\Http\Controllers\V2\Regional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// modules
use App\Modules\RegionalModules;

// rules
use App\Rules\Regional\AddRegional;
use App\Rules\Regional\EditRegional;

class RegionalController extends Controller
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
            return RegionalModules::getAll($request);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function getById(string $regionalId)
    {
        try {
            return RegionalModules::getById($regionalId);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function add(AddRegional $request)
    {
        try {
            return RegionalModules::add($request);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function edit(EditRegional $request, string $regionalId)
    {
        try {
            return RegionalModules::edit($request, $regionalId);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }
}
