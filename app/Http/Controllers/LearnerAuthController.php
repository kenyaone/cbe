<?php

namespace App\Http\Controllers;

use App\Models\Learner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LearnerAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('learner.auth.login');
    }

    public function showRegisterForm()
    {
        return view('learner.auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('learner')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            Auth::guard('learner')->user()->update(['last_login_at' => now()]);
            return redirect()->intended(route('learner.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:learners',
            'password' => 'required|string|min:6|confirmed',
            'grade_level' => 'required|string',
            'admission_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
        ]);

        $learner = Learner::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'grade_level' => $validated['grade_level'],
            'admission_number' => $validated['admission_number'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        Auth::guard('learner')->login($learner);

        return redirect()->route('learner.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('learner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('learner.login'));
    }

    public function profile()
    {
        $learner = Auth::guard('learner')->user();
        return view('learner.profile', compact('learner'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:learners,email,' . Auth::guard('learner')->id(),
            'phone' => 'nullable|string|max:20',
            'grade_level' => 'required|string',
        ]);

        Auth::guard('learner')->user()->update($validated);

        return redirect()->route('learner.profile')->with('success', 'Profile updated successfully');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $learner = Auth::guard('learner')->user();

        if (!Hash::check($request->current_password, $learner->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $learner->update(['password' => Hash::make($request->password)]);

        return redirect()->route('learner.profile')->with('success', 'Password changed successfully');
    }
}
