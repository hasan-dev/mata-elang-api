<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizationResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function delete($id) {
        $data = Organization::findOrFail($id);
        $data->delete();
        return response()->json([
            'code' => 200,
            'message' => 'Success Deleted',
        ], 200);
    }

    public function create(Request $request) {
        $validatedData = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'address' => ['required', 'string'],
            'province' => ['required', 'string'],
            'city' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'website' => ['required', 'string'],
            'oinkcode' => ['required', 'string'],
            'parent_id' => ['integer']
        ]);

        $user = auth()->user();
        $organisationIds = $user->organizations()->pluck('user_id');

        try {
            $organization = Organization::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'address' => $validatedData['address'],
                'province' => $validatedData['province'],
                'city' => $validatedData['city'],
                'phone_number' => $validatedData['phone_number'],
                'website' => $validatedData['website'],
                'oinkcode' => $validatedData['oinkcode'],
                'website' => $validatedData['website'],
                'parent_id' => $organisationIds[0],
            ]);

            return response()->json([
                'code' => 201,
                'message' => 'created',
                'data' => new OrganizationResource($organization)
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
        $validatedData = $request->validate([
            'name' => ['string'],
            'email' => ['email', 'max:255'],
            'address' => ['string'],
            'province' => ['string'],
            'city' => ['string'],
            'phone_number' => ['string'],
            'oinkcode' => ['string'],
            'website' => ['string']
        ]);

        try {
            $organization = Organization::findOrFail($id);
            $organization->update([
                'name' => $validatedData['name'] ?? $organization->name,
                'email' => $validatedData['email'] ?? $organization->email,
                'address' => $validatedData['address'] ?? $organization->address,
                'province' => $validatedData['province'] ?? $organization->province,
                'city' => $validatedData['city'] ?? $organization->city,
                'phone_number' => $validatedData['phone_number'] ?? $organization->phone_number,
                'website' => $validatedData['website'] ?? $organization->website,
                'oinkcode' => $validatedData['oinkcode'] ?? $organization->oinkcode,
                'website' => $validatedData['website'] ?? $organization->website,
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'success',
                'data' => new OrganizationResource($organization)
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
                ->where('sensors.status', '!=',  'deleted')
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
                ->join('users', 'assets.pic_id', '=', 'users.id')
                ->join('sensors', 'assets.sensor_id', '=', 'sensors.id')
                ->select('assets.id', 'assets.name', 'assets.description', 'organizations.id as organization_id',
                'organizations.name as organization_name', 'users.id as user_id',
                'users.name as user_name', 'sensors.id as sensor_id', 'sensors.name as sensor_name')
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
            $data = Role::where('organization_id', $id)->get();
            return response()->json([
                'code' => 200,
                'message' => 'success',
                'data' => RoleResource::collection($data)
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
            $organization = Organization::findOrFail($id);
            $users = $organization->users;

            return response()->json([
                'code' => 200,
                'message' => 'success',
                'data' => UserResource::collection($users)->unique('id')
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
