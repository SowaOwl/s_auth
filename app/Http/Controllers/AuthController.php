<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    )
    {
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = request(['email', 'password']);

        $response = $this->authService->login($credentials);

        if (!$response) {
            return $this->SendErrorResponse(trans('auth.login.incorrect_credentials'), null);
        } elseif ($response['success']) {
            return $this->SendSuccessResponse(trans('auth.login.success'), $response['data']);
        } else {
            return $this->SendErrorResponse(trans('auth.login.error'), $response['error']);
        }
    }

    public function registration(RegistrationRequest $request): JsonResponse
    {
        $response = $this->authService->registration([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($response['success']) {
            return $this->SendSuccessResponse(trans('auth.registration.success'), $response['user']);
        } else {
            return $this->SendErrorResponse(trans('auth.registration.error'), $response['error']);
        }
    }
}
