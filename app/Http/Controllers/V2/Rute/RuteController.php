<?php

namespace App\Http\Controllers\V2\Rute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// modules
use App\Modules\RuteModules;

// rules
use App\Rules\Rute\AddRute;
use App\Rules\Rute\EditRute;

class RuteController extends Controller
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
            return RuteModules::getAll($request);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function getById(string $ruteId)
    {
        try {
            return RuteModules::getById($ruteId);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function add(AddRute $request)
    {
        try {
            return RuteModules::add($request, self::$myId);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }

    public function edit(string $ruteId, EditRute $request)
    {
        try {
            return RuteModules::edit($ruteId, $request, self::$myId);
        } catch (\Exception $e) {
            self::$jsonData['status']   = $e->getCode() ? $e->getCode() : 500;
            self::$jsonData['message']  = $e->getMessage();
        }
        return response()->json(self::$jsonData, self::$jsonData['status']);
    }
}
