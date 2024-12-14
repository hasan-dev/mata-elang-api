<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;


class UserController extends Controller
{
    //



    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'login',
            'logout',
            'getOrganizationbyUserId',
            'registerFirst',
        ]]);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
            $email = $request->input('email');
            $password = $request->input('password');
            $data = User::where('email', $email)->first();

            if (Hash::check($password, $data->password)) {
                if (!$token = auth('api')->login($data)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                } else {
                    $user = User::where('email', $request->email)->first();
                    $user->save();
                    $data = $this->respondWithToken($token, $data);
                    return $data;
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'website'  => ['string'],
            'address' => ['string'],
            'birth_date' => ['string'],
            'organization_ids' => 'array',
            'organization_ids.*' => 'integer|exists:organizations,id',
            'role_ids' => 'array',
            'role_ids.*' => 'integer|exists:roles,id',
        ]);

        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone_number' => $request->input('phone_number'),
                'password' => Hash::make($validatedData['password'])
            ]);

            $organizationIds = $request->input('organization_ids');
            $roleIds = $request->input('role_ids');

            $organizations = Organization::whereIn('id', $organizationIds)->get();
            $roles = Role::whereIn('id', $roleIds)->get();

            foreach ($organizations as $organization) {
                foreach ($roles as $role) {
                    $user->organizations()->attach($organization, ['role_id' => $role->id]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Register success',
                'data' => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function profile($id)
    {
        $data = User::find($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => new UserResource($data)
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => ['string'],
            'email' => ['email'],
            'password' => ['string'],
            'phone_number' => ['string'],
            'website'  => ['string'],
            'address' => ['string'],
            'birth_date' => ['string'],
            'organization_ids' => 'array',
            'organization_ids.*' => 'integer',
            'role_ids' => 'array',
            'role_ids.*' => 'integer',
        ]);

        $user = User::findOrFail($id);

        // Update data user
        $user->name = $validatedData['name'] ?? $user->name;
        $user->email = $validatedData['email'] ?? $user->email;
        $user->phone_number = $validatedData['phone_number'] ?? $user->phone_number;
        $user->password = isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password;
        $user->website = $validatedData['website'] ?? $user->website;
        $user->address = $validatedData['address'] ?? $user->address;
        $user->birth_date = $validatedData['birth_date'] ?? $user->birth_date;
        $user->save();

        if (isset($validatedData['organization_ids'])) {
            $user->organizations()->sync($validatedData['organization_ids']);
        } else {
            $user->organizations()->detach();
        }

        if (isset($validatedData['role_ids'])) {
            foreach ($validatedData['organization_ids'] as $organizationId) {
                $user->roles()->sync($validatedData['role_ids'], ['organization_id' => $organizationId]);
            }
        } else {
            $user->roles()->detach();
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Update success',
            'data' => new UserResource($user)
        ], 200);
    }

    public function updateProfile(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => ['string'],
            'email' => ['email'],
            'password' => ['string'],
            'phone_number' => ['string'],
            'website'  => ['string'],
            'address' => ['string'],
            'birth_date' => ['string']
        ]);

        $user = User::findOrFail($id);

        // Update data user
        $user->name = $validatedData['name'] ?? $user->name;
        $user->email = $validatedData['email'] ?? $user->email;
        $user->phone_number = $validatedData['phone_number'] ?? $user->phone_number;
        $user->password = isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password;
        $user->website = $validatedData['website'] ?? $user->website;
        $user->address = $validatedData['address'] ?? $user->address;
        $user->birth_date = $validatedData['birth_date'] ?? $user->birth_date;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Update success',
            'data' => new UserResource($user)
        ], 200);
    }

    public function getOrganizationbyUserId($id)
    {
        $data = User::find($id);
        $organizations = $data->organizations;
        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => $organizations
        ], 200);
    }

    public function registerFirst(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'website'  => ['string'],
            'address' => ['string'],
            // 'birth_date' => ['string'],
            'organization_ids' => 'array',
            'organization_ids.*' => 'integer|exists:organizations,id',
            'role_ids' => 'array',
            'role_ids.*' => 'integer|exists:roles,id',
        ]);

        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone_number' => $request->input('phone_number'),
                'password' => Hash::make($validatedData['password']),
                'website' => $validatedData['website'],
                'address' => $validatedData['address'],
            ]);

            $organizationIds = $request->input('organization_ids');
            $roleIds = $request->input('role_ids');

            $organizations = Organization::whereIn('id', $organizationIds)->get();
            $roles = Role::whereIn('id', $roleIds)->get();

            foreach ($organizations as $organization) {
                foreach ($roles as $role) {
                    $user->organizations()->attach($organization, ['role_id' => $role->id]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Register success',
                'data' => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function delete($id)
    {
        $data = User::find($id);
        $data->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Delete success'
        ], 200);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $data)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * (60 * 24 * 30),
            'data' => new UserResource($data)
        ]);
    }

    public function refresh()
    {
        $token = JWTAuth::getToken();
        $newToken = JWTAuth::refresh($token, true);
        return response()->json([
            'code' => 200,
            'access_token' => $newToken
        ], 200);
    }
}
