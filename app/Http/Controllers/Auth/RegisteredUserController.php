<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // States fetch karna premium dropdown ke liye
        $states = DB::table('states')->orderBy('name')->get();
        return view('auth.register', compact('states'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'mobile' => ['nullable', 'numeric', 'digits_between:10,15'],
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'state_id' => $request->state_id,
            'city' => $request->city,
            'password' => Hash::make($request->password),
            'is_active' => 1, // Default active logic
        ]);

        // --- NEW: Assign Student Role automatically ---
        $user->assignRole('Student');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
