<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'public_id' => (string) Str::ulid(),
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'status' => 'active',
            'locale' => 'en',
            'timezone' => 'UTC',
        ]);
    }
}
