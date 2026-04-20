<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Google\Client as GoogleClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GoogleAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login', [
            'googleClientId' => (string) config('services.google.client_id', ''),
        ]);
    }

    public function authenticate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'credential' => ['required', 'string'],
        ]);

        $clientId = (string) config('services.google.client_id', '');

        if ($clientId === '') {
            throw ValidationException::withMessages([
                'credential' => 'Google Client ID nao configurado.',
            ]);
        }

        $client = new GoogleClient(['client_id' => $clientId]);
        $payload = $client->verifyIdToken($data['credential']);

        if (! is_array($payload) || empty($payload['sub']) || empty($payload['email'])) {
            throw ValidationException::withMessages([
                'credential' => 'Token Google invalido.',
            ]);
        }

        if (($payload['email_verified'] ?? false) !== true) {
            throw ValidationException::withMessages([
                'credential' => 'Email do Google nao verificado.',
            ]);
        }

        $email = (string) $payload['email'];
        $googleId = (string) $payload['sub'];

        $user = User::where('google_id', $googleId)->first();

        if (! $user) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = new User();
            $user->email = $email;
            $user->password = Hash::make(Str::random(40));
        }

        $user->name = (string) ($payload['name'] ?? Str::before($email, '@'));
        $user->google_id = $googleId;
        $user->avatar_url = (string) ($payload['picture'] ?? '');
        $user->email_verified_at = now();
        $user->save();

        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json([
            'ok' => true,
            'redirect' => route('dashboard'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
