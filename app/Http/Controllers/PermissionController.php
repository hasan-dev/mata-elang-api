<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    //
    public function get() {
        $data = Permission::all();
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $data
        ], 200);
    }
}
