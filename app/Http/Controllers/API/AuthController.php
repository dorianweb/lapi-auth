<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'mi', 'validat']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $id = User::where(['email' => auth()->user()->email])->get()[0]->id;

        $token = Token::create([
            'jwt' => $token,
            'expire' => Carbon::now()->timestamp + auth()->factory()->getTTL() * 60,
            'user_id' => $id,
        ]);
        return $this->respondWithToken($token);
    }
    function validat($token)
    {
        $token = Token::where(['jwt' => $token])->firstOrFail();
        $result['access_token'] = $token->jwt;

        $time = $token->expire - Carbon::now()->timestamp;
        if ($time < 0) {
            $result['expired'] = true;
        } else {
            $result['expired'] = false;
            $result['expire_in'] = $time;
            $result['expire_at'] = $token->expire;
        }

        return $result;
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {

        return response()->json(auth()->user());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function mi()
    {
        return User::all();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = explode(' ', request()->header('Authorization'))[1];

        $token = Token::where(['jwt' => $token])->firstOrFail();
        if (!$this->isTokenValid($token)) {
            $neotoken = auth()->refresh();
            $token->jwt = $neotoken;
            $token->expire = Carbon::now()->timestamp + auth()->factory()->getTTL() * 60;
            $token->save();
            return $this->respondWithToken($neotoken);
        } else {
            return $this->respondWithToken($token);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    function isTokenValid($token)
    {
        $token = Token::where(['jwt' => $token])->firstOrFail();
        $result['access_token'] = $token->jwt;
        $time = $token->expire - Carbon::now()->timestamp;
        return $time < 0;
    }
}
