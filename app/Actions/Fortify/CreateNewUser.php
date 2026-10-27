<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Services\LegalConsent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'terms' => ['required', 'accepted'],
        ], ['terms.required' => 'Debes aceptar los términos y la política de privacidad.', 'terms.accepted' => 'Debes aceptar los términos y la política de privacidad.'])->validate();

        return DB::transaction(function () use ($input): User {
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role' => 'client',
        ]);
        app(LegalConsent::class)->record($user, 'password');
        return $user;
        });
    }
}
