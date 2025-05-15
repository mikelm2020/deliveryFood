<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Catch_;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        if(!$token){
            return response()->json([
                'error' => 'Token no proporcionado'
            ],Response::HTTP_UNAUTHORIZED);
        }
        try{
            $token = str_replace('Bearer ', '', $token);
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'),'HS256'));
            return $next($request);

        }catch(ExpiredException $e){
            return response()->json([
                'error' => 'Token ha expirado'
            ],Response::HTTP_UNAUTHORIZED);
        }catch(Exception $e){
            return response()->json([
                'error' => 'Token es inválido: '.$e->getMessage()
            ],Response::HTTP_UNAUTHORIZED);
        }

    }
}
