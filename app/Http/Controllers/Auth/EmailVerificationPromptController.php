<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if (! $request->user()->hasVerifiedEmail()) {
            return view('auth.verify-email');
        }

        $destino = match ($request->user()->rol) {
            'coordinacion' => route('dashboard', absolute: false),
            'tutor' => route('mis-asignaciones', absolute: false),
            default => '/',
        };

        return redirect()->intended($destino);
    }
}
