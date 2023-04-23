<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizationMemberResource;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationMemberController extends Controller
{
    //
    public function get($id) {
        $data = OrganizationMember::find($id);
        // dd($data->organization);
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => new OrganizationMemberResource($data)
        ], 200);
    }

    public function registerUser(Request $request, $id)
    {
        $request->validate([
            'name' => ['string'],
            'email' => ['string', 'email', 'unique:users'],
            'password' => ['string'],
            'phone_number' => ['string'],
            'photo' => ['string'],
            'role_id' => ['integer']
        ]);

        try {
            $data = new User;
            $data->name = $request->input('name');
            $data->email = $request->input('email');
            $data->password = Hash::make($request->input('password'));
            $data->phone_number = $request->input('phone_number');
            $data->photo = $request->input('photo');
            $data->save();

            $member = new OrganizationMember;
            $member->organization_id = $id;
            $member->user_id = $data->id;
            $member->role_id = $request->input('role_id');
            $member->save();

            return response()->json([
                'code ' => 201,
                'message' => 'registered',
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

    public function editUser(Request $request, $id)
    {
        $request->validate([
            'user_id' => ['required', 'integer'],
            'roles.role_id' => ['integer']
        ]);

        try {
            foreach($request->roles as $role) {
                $member = OrganizationMember::where('user_id', $request->input('user_id'))->where('role_id', $role['role_id'])->first();
                if($member === null) {
                    $member = new OrganizationMember;
                    $member->organization_id = $id;
                    $member->user_id = $request->input('user_id');
                    $member->role_id = $role['role_id'];
                    // 
                    $member->save();
                }
            }

            $data = OrganizationMember::where('user_id', $request->input('user_id'))->get();
            return response()->json([
                'code' => 200,
                'message' => 'updated',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createAdmin(Request $request) {
        $request->validate([
            'organization_id' => ['required'],
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required'],
            'phone_number' => ['required']
        ]);

        try {
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->phone_number = $request->input('phone_number');
            $user->photo = $request->input('photo');
            $user->save();

            $role = Role::where('name', 'admin')->first();

            $data = new OrganizationMember;
            $data->organization_id = $request->input('organization_id');
            $data->user_id = $user->id;
            $data->role = $role->id;
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
}
