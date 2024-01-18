<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param UserRequest $request The user registration request.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public function register(UserRequest $request): JsonResponse
    {
        // Create a new user
        User::create(
            [
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
            ]
        );

        // Return JSON response
        return response()->json(
            [
                'message' => 'User successfully registered',
            ]
        );
    }

    /**
     * Logs in a user and returns a JSON response with a token.
     *
     * @param LoginRequest $request The login request object.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Validate the login credentials
        $credentials = $request->validated();

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // Regenerate the session
            $request->session()->regenerate();

            // Get the authenticated user
            $user = Auth::user();

            // Create a token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return a JSON response with the token
            return response()->json(
                [
                    'message' => 'User successfully logged in',
                    'token' => $token,
                    'user' => $user,
                ]
            );
        }

        // Return a JSON response with an error message
        return response()->json(['error' => 'The provided credentials do not match our records.'], 401);
    }

    /**
     * Validate token and return user information.
     *
     * @param  Request  $request  The HTTP request object.
     * @return \Illuminate\Http\JsonResponse  The JSON response containing the user information.
     */
    public function validateToken(Request $request)
    {
        // Get the authenticated user from the request object
        $user = $request->user();

        // Return the user information in a JSON response
        return response()->json(['user' => $user]);
    }
}
