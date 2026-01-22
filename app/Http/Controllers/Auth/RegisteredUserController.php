<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB; // Database se States lane ke liye

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Database se States ki list nikalein taaki dropdown me dikha sakein
        $states = DB::table('states')->orderBy('name')->get();

        return view('auth.register', compact('states'));
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validation (Input check karna)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // LMS Fields Validation
            'mobile' => ['nullable', 'numeric', 'digits_between:10,15'],
            'state_id' => ['nullable', 'exists:states,id'], // Check karega ki state ID valid hai ya nahi
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
        ]);

        // 2. User Create karna
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

            // LMS Extra Fields save karna
            'mobile' => $request->mobile,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'state_id' => $request->state_id,
            'city' => $request->city,
            'is_active' => 1, // Default user active rahega
        ]);

        // 3. Default Role Assign karna (Optional)
        // Agar aap chahte hain ki naya user automatically 'Student' ban jaye:
        // $user->assignRole('Student');

        // 4. Registration Event Fire karna
        event(new Registered($user));

        // 5. User ko auto-login karana
        Auth::login($user);

        // 6. Dashboard par redirect karna
        return redirect()->route('dashboard');
    }
}
