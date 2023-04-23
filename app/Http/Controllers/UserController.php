<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\OrganizationMember;
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
        $request->validate([
            'name' => ['required','string'],
            'email' => ['required','string', 'email'],
            'password' => ['required','string'],
            'phone_number' => ['required','string'],
            'photo' => ['required']
        ]);
        
        try {
            $data = new User;
            $data->name = $request->input('name');
            $data->email = $request->input('email');
            $data->password = Hash::make($request->input('password'));
            $data->phone_number = $request->input('phone_number');
            $data->photo = $request->input('photo');
            $data->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Register success',
                'data' => $data
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
        $request->validate([
            'name' => ['required','string'],
            'email' => ['required','string', 'email'],
            'password' => ['required','string'],
            'phone_number' => ['required','string'],
            'photo' => ['required'],
        ]);
        
        try {
            $data = User::find($id);
            $data->name = $request->input('name');
            $data->email = $request->input('email');
            $data->password = Hash::make($request->input('password'));
            $data->phone_number = $request->input('phone_number');
            $data->photo = $request->input('photo');
            $data->save();

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
            'expires_in' => auth('api')->factory()->getTTL() * 60,
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
