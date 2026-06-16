<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'correo' => ['required', 'email'],
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe tener un formato válido.',
        ]);

        $usuario = User::query()
            ->where('correo', $request->input('correo'))
            ->first();

        if (! $usuario || ! $usuario->activo) {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors([
                    'correo' => 'No encontramos una cuenta activa con ese correo.',
                ]);
        }

        $status = Password::sendResetLink([
            'correo' => $usuario->correo,
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Se envió un enlace para establecer o restablecer tu contraseña.')
            : back()
            ->withInput($request->only('correo'))
            ->withErrors([
                'correo' => 'No fue posible enviar el enlace. Verifica la configuración SMTP.',
            ]);
    }
}
