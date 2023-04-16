<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    //
    public function create(Request $request) {
        $request->validate([
            'role_id' => 'integer',
            'permission.permission_id' => 'integer'
        ]);

        foreach($request->permission as $role_permission) {
            $rolePermission = new RolePermission();
            $rolePermission->permission_id = $role_permission['permission_id'];
            $rolePermission->role_id = $request->input('role_id');
            $rolePermission->save();
        }

        $data = RolePermission::where('role_id', $request->role_id)->get();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function delete(Request $request) {
        $request->validate([
            'role_id' => 'integer',
            'permission_id' => 'integer'
        ]);
        
        $data = RolePermission::where('role_id', $request->role_id)
            ->where('permission_id', $request->permission_id)
            ->firstOrFail();
        $data->delete();

        return response()->json([
            'code' => 200,
            'message' => 'success'
        ], 200);
    }
}
