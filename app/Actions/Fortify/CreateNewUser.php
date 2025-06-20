<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Auth\Events\Registered;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique(User::class),
            ],
            'birthdate' => [
                'required',
                'string',
                'max:20',
            ],
            'phone_number' => [
                'required',
                'string',
                'max:40',
                Rule::unique(User::class)
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'birthdate' => $input['birthdate'],
            'phone_number' => $input['phone_number'],
            'password' => Hash::make($input['password'])
        ]);

        event(new Registered($user)); // 👈 importante

        return $user;
    }
}
