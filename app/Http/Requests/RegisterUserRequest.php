<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'location' => 'required|string',
            'skills' => 'required|string',
            'github_url' => 'nullable|url',
            'bio' => 'required|string|min:50|max:500',
            'portfolio' => 'nullable|string',
            'interests' => 'nullable|string',
            'current_position' => 'nullable|string',
            'languages' => 'nullable|string'
        ];
    }
}
