<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Redis;
use JetBrains\PhpStorm\ArrayShape;

class AuthService
{
    /**
     * @param array $params
     * @return array
     */
    public function registration(array $params): array
    {
        try {
            $user = User::create($params);

            return [
                'success' => true,
                'user'    => $user
            ];
        } catch (Exception $exception) {
            return $this->handleError($exception);
        }
    }

    /**
     * @param array $params
     * @return array|false
     */
    public function login(array $params): bool|array
    {
        try {
            if (!$token = auth()->attempt($params)) {
                return false;
            }
            $user = User::query()->where('email', $params['email'])->first();

            $this->SaveUserToRedis($user->id, $user);

            return [
                'success' => true,
                'data' => [
                    'token'   => $token,
                    'expires_in' => auth()->factory()->getTTL(),
                    'user'    => $user,
                ]
            ];
        } catch (Exception $exception) {
            return $this->handleError($exception);
        }
    }

    /**
     * @param Exception $exception
     * @return array
     */
    #[ArrayShape(['success' => "false", 'error' => "string"])]
    private function handleError(Exception $exception): array
    {
        return [
            'success' => false,
            'error'   => $exception->getMessage()
        ];
    }

    private function SaveUserToRedis($user_id, $user)
    {
        $redis = Redis::connection();
        $redis->set('users' . $user_id, json_encode($user));
        $redis->expire('users' . $user_id, config('jwt.refresh_ttl'));
    }
}
