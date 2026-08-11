<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/register",
        summary: "Forjar Aliança (Registro)",
        description: "Registra um novo guerreiro em Midgard e retorna o Token de acesso",
        tags: ["Autenticação"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "username", "email", "password"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Ragnar Lothbrok"),
                new OA\Property(property: "username", type: "string", example: "ragnar_dev"),
                new OA\Property(property: "email", type: "string", format: "email", example: "guerreiro@midgard.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "senha123")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Registro bem-sucedido")]
    #[OA\Response(response: 422, description: "Erro de validação (ex: e-mail já em uso)")]
    public function register(Request $request)
    {
        // Validação obrigatória dos campos definidos no seu plano
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Geração do token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    #[OA\Post(
        path: "/login",
        summary: "Atravessar os portões (Login)",
        description: "Autentica um guerreiro e retorna o Token de acesso",
        tags: ["Autenticação"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "guerreiro@midgard.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "senha123")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Login bem-sucedido")]
    #[OA\Response(response: 422, description: "Credenciais inválidas")]
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Verifica se o usuário existe e se a senha bate
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    #[OA\Post(
        path: "/logout",
        summary: "Partir do Salão (Logout)",
        description: "Encerra a sessão atual do guerreiro, invalidando o token",
        tags: ["Autenticação"],
        security: [["sanctum" => []]]
    )]
    #[OA\Response(response: 200, description: "Sessão encerrada com sucesso")]
    #[OA\Response(response: 401, description: "Não autenticado")]
    public function logout(Request $request)
    {
        // Deleta o token atual, encerrando a sessão
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sessão encerrada com sucesso'
        ]);
    }
}