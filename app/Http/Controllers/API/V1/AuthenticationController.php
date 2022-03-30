<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Auth;

use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\LoginRequest;

use App\Models\User;

class AuthenticationController extends Controller
{

    use ApiResponse;

    /**
     * Register a New User.
     *
     * @bodyParam   name    string  required    The name of the  user.      Example: Arvin Almario
     * @bodyParam   email    string  required    The email of the  user.      Example: arvin@almar.io
     * @bodyParam   password    string  required    The password of the  user.   Example: ultimatepassword
     *
     * @param   $request    RegistrationRequest    
     * @return  $response   Request Response in JSON
     * 
     * @response {
     *  "token": "eyJ0eXA..."
     * }
     */
    public function register(RegistrationRequest $request)
    {
       
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'password' => bcrypt($data['password']),
            'email' => $data['email']
        ]);

        return $this->success([
            'token' => $user->createToken('tokens')->plainTextToken
        ]);
    }

    /**
     * Login and request a token using email and password.
     *
     * @bodyParam   email    string  required    The email of the  user.      Example: arvin@almar.io
     * @bodyParam   password    string  required    The password of the  user.   Example: ultimatepassword
     *
     * @param   $request    LoginRequest    Validation of POST Login Request 
     * @return  $response   Request Response in JSON
     * 
     * 
     * @response {
     *  "token": "eyJ0eXA..."
     * }
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        // if allowed to use HTTP 422 this can be implemented within
        // the validation rules 
        // see App\Http\Requests\LoginRequest
        if(empty(User::where('email', $data['email'])->first())){
            return $this->notfound('Email does not exist', 'message');
        }
        
        if (!Auth::attempt($data)) {
            return $this->error('Invalid Credentials', 401);
        }

        return $this->success([
            'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]);
    }

    /**
     * Logout or destroy Token.
     * 
     * This method destroys your token
     * 
     * - 401 on invalid authentication
     * - 204 NO CONTENT on success
     *
     * @param   $request    RegistrationRequest    
     * @return  $response   Request Response in JSON
     * 
     * @header Authorization Bearer 4|Ikzlly2IHmq81rPgq6a7S18jRHtI0AUV32orS5sG
     * @authenticated
     * @response {
     *  "token": "eyJ0eXA..."
     * }
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return $this->respondNoContent();
    }
}
