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
        ]]);
    }

    public function login(Request $request) {
        try {
            $request->validate([
                'email' => ['required','email'],
                'password' => ['required'],
            ]);
            $email = $request->input('email');
            $password = $request->input('password');
            $data = User::where('email', $email)->first();

            if(Hash::check($password, $data->password))
            {
                if(! $token = auth('api')->login($data)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                } else {
                    $user = User::where('email', $request->email)->first();
                    $user->save();
                    return $this->respondWithToken($token, $data);
                }
            }
        }catch(\Exception $e){
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ],401);
        }
    }

    public function register(Request $request) {
        $validatedData = $request->validate([
            'name' => ['required','string'],
            'email' => ['required','string', 'email'],
            'password' => ['required','string'],
            'phone_number' => ['required','string'],
            'photo' => ['string'],
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
                'photo' => $request->input('photo'),
                'password' => Hash::make($validatedData['password'])
            ]);

            if (isset($validatedData['organization_ids'])) {
                $organizations = Organization::find($validatedData['organization_ids']);
                $user->organizations()->attach($organizations);
            }

            if(isset($validatedData['role_ids'])) {
                $roles = Role::find($validatedData['role_ids']);
                $user->roles()->attach($roles);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Register success',
                'data' => new UserResource($user)
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ],401);
        }
    }

    public function profile($id) {
        $data = User::find($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Success',
            'data' => new UserResource($data)
        ], 200);
    }

    public function update(Request $request, $id) {
        $validatedData = $request->validate([
            'name' => ['string'],
            'email' => ['email'],
            // 'password' => ['string'],
            'phone_number' => ['string'],
            'photo' => ['string'],
            'organization_ids' => 'array',
            'organization_ids.*' => 'integer|exists:organizations,id',
            'role_ids' => 'array',
            'role_ids.*' => 'integer|exists:roles,id',
        ]);
        
        try {
            $data = User::find($id);
            // dd($data->password);
            $data->update([
                'name' => $validatedData['name'] ?? $data->name,
                'email' => $validatedData['email'] ?? $data->email,
                'phone_number' => $validatedData['phone_number'] ?? $data->phone_number,
                'photo' => $validatedData['photo'] ?? $data->photo,
                // 'password' => Hash::make($validatedData['password']) ?? $data->password
            ]);

            if (isset($validatedData['organization_ids'])) {
                $organizations = Organization::find($validatedData['organization_ids']);
                $data->organizations()->sync($organizations);
            }

            if(isset($validatedData['role_ids'])) {
                $roles = Role::find($validatedData['role_ids']);
                $data->roles()->sync($roles);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Update success',
                'data' => $data
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ],401);
        }
    }

    public function delete($id) {
        $data = User::find($id);
        $data->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Delete success'
        ], 200);
    }


         /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token,$data)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * (60 * 24 * 30),
            'data' => $data
        ]);
    }

    public function refresh() {
        $token = JWTAuth::getToken();
        $newToken = JWTAuth::refresh($token, true);
        return response()->json([
            'code' => 200,
            'access_token' => $newToken 
        ], 200);
    }
}
