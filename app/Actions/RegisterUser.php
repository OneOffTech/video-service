<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Action to register a user
 */
class RegisterUser
{
    /**
     * Register a user
     *
     * @param array{name:string, email:string, password:string, password_confirmation:string} $data The user data
     * @throws \Illuminate\Validation\ValidationException
     * @return User the user created
     */
    public function __invoke(array $data): User
    {
        $validated = Validator::validate($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        return $user;
    }
}
