<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function() use ($request, &$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'customer',
            ]);

            // buat alamat default
            Address::create([
                'user_id'        => $user->id,
                'label'          => 'Utama',
                'recipient_name' => $user->name,
                'phone'          => $user->phone,
                'address_line'   => $request->address,
                'province'       => $request->input('province'),
                'city'           => $request->input('city'),
                'district'       => $request->input('district'),
                'village'        => $request->input('village'),
                'postal_code'    => $request->input('postal_code'),
                'is_default'     => true,
            ]);
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('customer.home', absolute: false));
    }
}
