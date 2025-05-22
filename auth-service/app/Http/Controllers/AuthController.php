<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\UserRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $validatedData = $request->validated();
        $credentials = [
            "email" => $validatedData["email"],
            "password" => $validatedData["password"]
        ];

        try{
            if(!$token = JWTAuth::attempt($credentials)){
                return response()->json([
                    "error" => "Usuario o contraseña invalida"
                ], Response::HTTP_UNAUTHORIZED);
            }

        }catch(JWTException){
            return response()->json([
                "error" => "No se pudo generar el token"
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respondWithToken($token);
    }
    
    public function register(UserRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData["name"],
            'email' => $validatedData["email"],
            'password' => bcrypt($validatedData['password']),
            'role' => $validatedData["role"]
        ]);

        return response()->json([
            "message" => "Usuario registrado correctamente",
            "user" => $user
        ], Response::HTTP_CREATED);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        {
        $user = auth()->user();
        return response()->json($user);
    }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try{
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            return response()->json([
                "message" => "Sesión cerrada correctamente"
            ]);

        }catch(JWTException $e){
            return response()->json([
                "error" => "No se pudo cerrar la sesión, el token no es válido"
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try{
            $token = JWTAuth::getToken();
            $newToken = auth()->refresh();
            JWTAuth::invalidate($token);
            return $this->respondWithToken($newToken);

        }catch(JWTException $e){
            return response()->json([
                "error" => "Error al refrescar el token"
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
        ],Response::HTTP_OK);
    }
}