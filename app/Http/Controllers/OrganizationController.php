<?php

namespace App\Http\Controllers;

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
        $data = Organization::find($id);
        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => $data
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
            'oinkcode' => ['required', 'string']
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

    public function create_admin(Request $request, $id) {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required'],
            'phone_number' => ['required','string'],
            'photo' => ['string']
        ]);

        try {
            $data = new User;
            $data->name = $request->input('name');
            $data->email = $request->input('email');
            $data->password = Hash::make($request->input('password'));
            $data->phone_number = $request->input('phone_number');
            $data->photo = $request->input('photo');

            $member = new OrganizationMember;
            $role  = Role::where('name', 'admin')->first();
            $member->organization_id = $id;
            $member->user_id = $data->id;
            $member->role_id = $role->id;
            $data->save();
            $member->save();

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

    public function get_sensor($id) {
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

    public function get_asset($id) {
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

    public function get_role($id) {
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

    public function get_user($id) {
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
