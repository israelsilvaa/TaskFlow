<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginFormRequest;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $regras = [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
        ];

        $feedback = [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser uma string.',
            'name.min' => 'O seu nome deve ter no mínimo :min letras.',
            'name.max' => 'O seu nome deve ter no máximo :max letras.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'O campo senha deve ser uma string.',
            'password.min' => 'A senha deve ter no mínimo :min caracteres.',
            'password.confirmed' => 'As senhas não são iguais.',
        ];

        $validator = Validator($request->all(), $regras, $feedback);

        if ($validator->fails()) {
            return response()->json([
                "error" => [
                    "status" => "422", "title" => "Unprocessable Entity", "detail" => $validator->errors()
                ]
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            "success" => [
                "status" => "201", "title" => "Created", "detail" => $user
            ]
        ], 201);
    }

    public function login(LoginFormRequest $request)
    {
        $request->validated();
        $credenciais = $request->all();

        $token = Auth('api')->attempt($credenciais);

        if ($token) {
            return response()->json([
                "success" => [
                    "status" => "200", "title" => "OK", "detail" => ['Token' => $token]
                ]
            ], 200);
        } else {
            return response()->json([
                "error" => [
                    "status" => "403", "title" => "Forbidden", "detail" => "Erro de usuário ou senha"
                ]
            ], 403);
        }
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json([
            "success" => [
                "status" => "200", "title" => "OK", "detail" => "Logout foi realizado com sucesso"
            ]
        ], 200);
    }

    public function refresh()
    {
        $token = auth('api')->refresh();

        return response()->json([
            "success" => [
                "status" => "200", "title" => "OK", "detail" => ['Token' => $token]
            ]
        ], 200);
    }

    public function me()
    {
        return response()->json([
            "success" => [
                "status" => "200", "title" => "OK", "detail" => ['User' => Auth()->user()]
            ]
        ], 200);
    }
}