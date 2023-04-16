<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //
     public function create(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'organization_id' => 'integer'
        ]);

        $data = new Role;
        $data->name = $request->name;
        $data->organization_id = $request->organization_id;
        $data->save();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $data
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
