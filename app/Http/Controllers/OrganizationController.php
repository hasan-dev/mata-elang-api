<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function profile($id) {
        $data = Organization::findOrFail($id);
        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => new OrganizationResource($data),
        ], 200);
    }

    public function create(Request $request) {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255', 'unique:organizations'],
            'address' => ['required', 'string'],
            'province' => ['required', 'string'],
            'city' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'website' => ['required', 'string'],
            'oinkcode' => ['required', 'string'],
            'parent_id' => ['integer']
        ]);

        try {
            $data = new Organization;
            $data->name = $request->input('name');
            $data->email = $request->input('email');
            $data->address = $request->input('address');
            $data->province = $request->input('province');
            $data->city = $request->input('city');
            $data->phone_number = $request->input('phone');
            $data->oinkcode = $request->input('oinkcode');
            $data->website = $request->input('website');
            $data->parent_id = $request->input('parent_id');
            $data->save();

            return response()->json([
                'code' => 201,
                'message' => 'created',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Request $request, $id) {
        $request->validate([
            'name' => ['string'],
            'email' => ['email', 'max:255'],
            'address' => ['string'],
            'province' => ['string'],
            'city' => ['string'],
            'phone' => ['string'],
            'oinkcode' => ['string'],
            'website' => ['string']
        ]);

        try {
            $data = Organization::findOrFail($id);
            $data->name = $request->input('name');
            $data->email = $request->input('email');
            $data->address = $request->input('address');
            $data->province = $request->input('province');
            $data->city = $request->input('city');
            $data->phone_number = $request->input('phone');
            $data->oinkcode = $request->input('oinkcode');
            $data->website = $request->input('website');
            $data->save();

            return response()->json([
                'code' => 200,
                'message' => 'success',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function getSensor($id) {
        try {
            $data = DB::table('sensors')
                ->join('organizations', 'sensors.organization_id', '=', 'organizations.id')
                ->select('sensors.*', 'organizations.id as organization_id', 'organizations.name as organization_name')
                ->where('sensors.organization_id', $id)
                ->get();
            return response()->json([
                'code' => 200,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function getAsset($id) {
        try {
            $data = DB::table('assets')
                ->join('organizations', 'assets.organization_id', '=', 'organizations.id')
                ->select('assets.*', 'organizations.id as organization_id', 'organizations.name as organization_name')
                ->where('assets.organization_id', $id)
                ->get();
            return response()->json([
                'code' => 200,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function getRole($id) {
        try {
            $data = Organization::where('id', $id)->with('role')->get();
            return response()->json([
                'code' => 200,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function getUser($id) {
        try {
            $data = OrganizationMember::where('organization_id', $id)->with('user')->get();
            return response()->json([
                'code' => 200,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }
    }
}
