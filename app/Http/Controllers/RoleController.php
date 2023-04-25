<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //
     public function create(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'organization_id' => 'integer',
            'permission_ids' => 'array',
            'permission_ids.*' => 'integer'
        ]);

        $role = Role::create([
            'name' => $validatedData['name'],
            'organization_id' => $validatedData['organization_id']
        ]);
        
        if(isset($validatedData['permission_ids'])) {
            $permissions = Permission::find($validatedData['permission_ids']);
            $role->permissions()->attach($permissions);
        }

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => new RoleResource($role)
        ], 200);
    }

    public function detail($id) {
        $data = Role::findOrFail($id);
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => new RoleResource($data)
        ], 200);
    }

    public function edit(Request $request, $id) {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'organization_id' => 'integer',
            'permission_ids' => 'array',
            'permission_ids.*' => 'integer'
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name' => $validatedData['name'] ?? $role->name,
            'organization_id' => $validatedData['organization_id'] ?? $role->organization_id
        ]);

        if(isset($validatedData['permission_ids'])) {
            $permissions = Permission::find($validatedData['permission_ids']);
            $role->permissions()->sync($permissions);
        }

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => new RoleResource($role)
        ], 200);
    }

    public function delete($id) {

        $data = Role::findOrFail($id);
        $data->delete();

        return response()->json([
            'code' => 200,
            'message' => 'success'
        ], 200);
    }
}
