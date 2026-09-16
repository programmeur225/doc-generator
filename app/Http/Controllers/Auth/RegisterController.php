<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('my-documents.index');
        }

        if ($request->filled('redirect')) {
            $redirect = $request->query('redirect');
            if (str_starts_with($redirect, url('/'))) {
                session(['url.intended' => $redirect]);
            }
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->sendEmailVerificationNotification();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->intended(route('my-documents.index'))
            ->with('success', 'Compte créé. Vérifiez votre boîte mail pour confirmer votre adresse.');
    }
}