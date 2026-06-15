<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    // REGISTER: Crea un usuario y devuelve su llave (token)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Ciframos la contraseña
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    #[OA\Post(
        path: "/api/v1/login",
        summary: "Iniciar sesión de usuario",
        tags: ["Autenticación"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", example: "nanche@nanche.com"),
                new OA\Property(property: "password", type: "string", example: "nanche")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Login correcto, devuelve el token")]
    #[OA\Response(response: 401, description: "Credenciales incorrectas")]
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Validamos que el usuario exista y la contraseña coincida
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        // Si pasa, le generamos su llave
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 200);
    }

    // LOGOUT: Destruye las llaves activas del usuario
    public function logout(Request $request)
    {
        // Revoca el token con el que se hizo la petición actual
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
    }

    // ME: Devuelve la información del usuario autenticado
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'rol'      => $user->rol, // Exponemos el rol
            'permisos' => [
                // Evaluamos el rol directamente, sin intermediarios
                'crear'    => in_array($user->rol, ['admin', 'editor']),
                'editar'   => in_array($user->rol, ['admin', 'editor']),
                'eliminar' => $user->rol === 'admin',
            ],
        ]);
    }
}
