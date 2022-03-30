<?php

namespace App\Http\Requests;

class RegistrationRequest extends AbstractRequest
{

    /**
     * Get the validation 
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email:rfc,dns|unique:users',
            'name' => 'required|string|max:50',
            'password' => 'required'
        ];
    }

     /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => 'Email is required.',
            'name.required' => 'Name is required.',
            'password.required' => 'Password is required.'
        ];
    }
}