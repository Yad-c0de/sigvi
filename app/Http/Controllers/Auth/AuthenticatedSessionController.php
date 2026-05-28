<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Procesa el inicio de sesión.
     */
    public function store(Request $request)
    {
        // Validar credenciales + rol
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
            'rol'      => ['required', 'in:Admin,Vendedor'],
        ]);

        // Intentar autenticar solo con email y password (sin rol aún)
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $user = Auth::user();

            // Verificar que el rol seleccionado coincida con el rol del usuario en BD
            if ($user->rol !== $request->rol) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'rol' => 'El rol seleccionado no coincide con tus permisos.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Si falla la autenticación básica
        throw ValidationException::withMessages([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Cierra la sesión.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
