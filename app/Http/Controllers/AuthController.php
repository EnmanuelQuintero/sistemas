<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nombre' => 'required|string',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('nombre', $credentials['nombre'])->first();

        if ($usuario && Hash::check($credentials['password'], $usuario->password)) {
            Auth::login($usuario);
            return redirect()->intended('/dashboard'); // cambia según tu app
        }

        return back()->withErrors([
            'nombre' => 'Credenciales incorrectas',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
