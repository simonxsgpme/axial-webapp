<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\OtpCode;
use App\Services\ApiService;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Send OTP code to user's email
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Aucun compte associé à cet email.',
            ])->onlyInput('email');
        }

        if (!$user->is_active) {
            return back()->withErrors([
                'email' => 'Votre compte est désactivé. Contactez l\'administrateur.',
            ])->onlyInput('email');
        }

        // Generate OTP code
        $code = OtpCode::generateCode();
        $expiresAt = Carbon::now()->addMinutes(10);

        // Save OTP code
        OtpCode::create([
            'email' => $request->email,
            'code' => $code,
            'expires_at' => $expiresAt,
        ]);

        // Send OTP via email using ApiService
        try {
            $apiService = new ApiService();
            $apiService->sendMail([
                'receiver_email' => $user->email,
                'receiver_name' => $user->first_name . ' ' . $user->last_name,
                'subject' => 'Code de connexion AXIAL',
                'message' => "Votre code de connexion est : <strong>{$code}</strong>. Ce code expire dans 10 minutes.",
                'action' => '',
                'action_text' => '',
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Erreur lors de l\'envoi du code. Veuillez réessayer.',
            ])->onlyInput('email');
        }

        // Store email in session for verification step
        $request->session()->put('otp_email', $request->email);

        return redirect()->route('login.verify')->with('success', 'Un code de connexion a été envoyé à votre adresse email.');
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyForm(Request $request)
    {
        if (!$request->session()->has('otp_email')) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp');
    }

    /**
     * Verify OTP code and login user
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $email = $request->session()->get('otp_email');

        if (!$email) {
            return redirect()->route('login')->withErrors([
                'code' => 'Session expirée. Veuillez recommencer.',
            ]);
        }

        // Find valid OTP code
        $otpCode = OtpCode::where('email', $email)
            ->where('code', $request->code)
            ->valid()
            ->latest()
            ->first();

        if (!$otpCode) {
            return back()->withErrors([
                'code' => 'Code invalide ou expiré.',
            ]);
        }

        // Mark OTP as used
        $otpCode->markAsUsed();

        // Login user
        $user = User::where('email', $email)->first();
        Auth::login($user, true); // Remember user

        $user->update(['last_login_at' => Carbon::now()]);

        $request->session()->forget('otp_email');
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
