<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'username' => 'required|string',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('learner')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            Auth::guard('learner')->user()->update(['last_login_at' => now()]);
            return redirect()->intended(route('learner.dashboard'));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['username'] . '@learner.local',
            'password' => Hash::make($validated['password']),
            'role' => 'learner',
        ]);

        Auth::guard('learner')->login($user);

        return redirect()->route('learner.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('learner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/learn/login');
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
            'username' => 'required|string|max:50|unique:users,username,' . Auth::guard('learner')->id(),
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
