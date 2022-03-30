<?php

namespace App\Http\Requests;

class LoginRequest extends AbstractRequest
{
   /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            // 'email' => 'required|string|email:rfc,dns|exists:users',
            'email' => 'required|string|email:rfc,dns',
            'password' => 'required|string|min:6'
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
            'email.exists' => 'Email does not exist.'
        ];
    }

}
