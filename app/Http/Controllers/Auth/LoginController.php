<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
{
    if (Auth::check()) {
        return redirect()->route(
            Auth::user()->isAdmin() ? 'admin.countries.index' : 'my-documents.index'
        );
    }

    // Mémoriser l'URL de retour (ex: téléchargement)
    if ($request->filled('redirect')) {
        $redirect = $request->query('redirect');
        if (str_starts_with($redirect, url('/'))) {
            session(['url.intended' => $redirect]);
        }
    }

    return view('auth.login');
}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Identifiants incorrects.']);
        }

        $request->session()->regenerate();

        return $request->user()->isAdmin()
            ? redirect()->intended(route('admin.countries.index'))
            : redirect()->intended(route('my-documents.index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('generate.countries');
    }
}
