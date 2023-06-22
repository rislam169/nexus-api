<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Service\UserContact;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    /**
     * @var UserContact
     */
    private $userService;

    public function __construct(UserContact $userService)
    {
        $this->userService = $userService;
    }

    /** 
     * Process sign up request
     * 
     * @param SignupRequest Request data from signup
     * @return $user and $token Token created after signup to perform login
     */
    public function signup(SignupRequest $request)
    {
        $data = $request->validated();

        $user = $this->userService->createUser([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return $this->success(compact('user', 'token'), 'Your account created successfully!');
    }

    /** 
     * Process login request
     * 
     * @param LoginRequest Request data from login form
     * @return $user and $token Token created after signup to perform login
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return $this->error(['message' => ['Email or password is incorrect']], null, 422);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;

        return $this->success(compact('user', 'token'), 'You are now loggedin!');
    }

    /** 
     * Process logout request
     * 
     * @param Request Request for logout user
     * @return Response Return successful message after logout
     */
    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'You have successfully logged out!');
    }
}
