<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends BaseController
{
    public function index()
    {
        $users = User::with('courses')->get();
        return response()->json(['data' => $users]);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            "email" => 'required|unique:users|email:rfc',
            'password' => 'required|regex:/^(?=.*[0-9])(?=.*[a-z]).{8,20}$/',
            'phone' => 'required|regex:/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/',
            'first_name' => 'regex:/^([a-z]{3,15})$/iu',
            'last_name' => 'regex:/^([a-z]{3,15})$/iu'
        ]);
        $user = User::create($request->all());

        return response()->json([
            "data" => collect($user)->except(['id', 'is_admin', 'password'])
        ],201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            "email" => 'unique:users|email:rfc',
            'password' => 'regex:/^(?=.*[0-9])(?=.*[a-z]).{8,20}$/',
            'phone' => 'regex:/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/',
            'first_name' => 'regex:/^([a-z]{3,15})$/iu',
            'last_name' => 'regex:/^([a-z]{3,15})$/iu'
        ]);

        $user = User::find($id);
        $user->update($request->json()->all());

        return response()->json([
            "data" => collect($user)->except(['id', 'is_admin','password'])
        ]);
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();

        return response()->json([
            'data' => [
                'message' => 'User deleted'
            ],
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            "email" => 'required',
            'password' => 'required',
        ]);

        $credentials = request(['email', 'password']);

        if (Auth::user()) {
            return response()->json([
                'data' => [
                    'message' => '[You already login'
                ]
            ]);
        }

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'data' => [
                    'error' => 'Unauthorized, check the entered data'
                ]
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'data' => [
                'message' => 'Successfully logged out'
            ]
        ]);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ]);
    }
}
