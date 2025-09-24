<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function registrar(Request $request) 
    {
        $dados = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $dados['password'] = bcrypt($dados['password']);
        $dados['picture'] = 'https://cdn0.iconfinder.com/data/icons/seo-web-4-1/128/Vigor_User-Avatar-Profile-Photo-02-1024.png';
        $dados['status'] = 'active';
        $dados['enabled'] = true;

        try {
            $usuario = User::create($dados);
            $token = $usuario->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuário registrado com sucesso.',
                'user' => $usuario,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao registrar o usuário.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credenciais = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = User::where('email', $credenciais['email'])->first();

        if (!$usuario || !Hash::check($credenciais['password'], $usuario->password)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'user' => $usuario,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

public function fotoUpload(Request $request)
{
    $request->validate([
        'picture' => 'required|image|mimes:jpg,jpeg,png|max:5120' // 5MB
    ]);

    $usuario = $request->user();

    if (!$request->hasFile('picture')) {
        return response()->json(['message' => 'Nenhuma imagem enviada.'], 400);
    }

    try {
        $path = $request->file('picture')->store('pictures', 'public');
        $url = asset('storage/' . $path); // ex: http://localhost:8000/storage/pictures/xxx.jpg

        // salva no banco (agora sim)
        $usuario->picture = $url;
        $usuario->save();

        return response()->json([
            'message' => 'Foto enviada e salva com sucesso.',
            'picture_url' => $url,
            'user' => $usuario
        ], 200);
    } catch (\Exception $e) {
        \Log::error('Erro no upload de foto: '.$e->getMessage());
        return response()->json(['message' => 'Erro ao enviar a foto.'], 500);
    }
}

    

    public function desativarConta(Request $request)
    {
        $usuario = $request->user();
        $usuario->update(['enabled' => false, 'status' => 'inactive']);

        return response()->json(['message' => 'Conta desativada com sucesso.']);
    }

    public function perfil(Request $request)
    {
        $usuario = $request->user();
        return response()->json($usuario);
    }

    public function editar(Request $request)
    {
        $usuario = $request->user();

        $dados = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:6|confirmed'
        ]);

        if (!empty($dados['password'])) {
            $dados['password'] = bcrypt($dados['password']);
        } else {
            unset($dados['password']);
        }

        $usuario->update($dados);

        return response()->json([
            'message' => 'Dados atualizados com sucesso.',
            'user' => $usuario
        ]);
    }
}
