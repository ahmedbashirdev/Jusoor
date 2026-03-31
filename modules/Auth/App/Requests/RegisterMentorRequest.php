<?php

namespace Modules\Auth\App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterMentorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'specialization' => ['required', 'string', 'max:255'],
            'years_of_experience' => ['required', 'string', 'in:0-1,1-3,3-5,5-10,10+'],
            'bio' => ['required', 'string', 'max:300'],
        ];
    }
}
