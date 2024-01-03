<?php

namespace App\Repositories\Users;

use App\Interfaces\Users\AuthServiceInterface;
use App\Interfaces\Users\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    protected $userServiceInterface;

    public function __construct(
        UserRepositoryInterface $userServiceInterface
    ) {
        $this->userServiceInterface = $userServiceInterface;
    }

    public function canUserLogin(array $data)
    {
        $findEmail = $this->userServiceInterface->find($data['email'], 'email');

        if (!$findEmail) {
            return false;
        }

        $findPassword = Hash::check($data['password'], $findEmail->password);

        if (!$findPassword) {
            return false;
        }

        return true;
    }

    public function userLogin(array $data)
    {
        $findEmail = $this->userServiceInterface->find($data['email'], 'email');

        if ($findEmail) {
            return auth()->login($findEmail);
        }

        return null;
    }
}
