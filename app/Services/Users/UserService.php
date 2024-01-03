<?php

namespace App\Services\Users;

use App\Interfaces\Users\UserServiceInterface;
use App\Interfaces\Users\UserRepositoryInterface;
use Exception;

class UserService implements UserServiceInterface
{
    protected $userService;

    public function __construct(
        UserRepositoryInterface $userService
    ) {
        $this->userService = $userService;
    }

    public function create(array $data)
    {
        $data['password'] = bcrypt($data['password']);

        return $this->userService->store($data);
    }

    public function validateUserCreation(array $data)
    {
        $findEmail = $this->userService->find($data['email'], 'email');

        if ($findEmail) {
            return [
                'canUserBeCreated' => false,
                'message' => 'Email already in use!'
            ];
        }

        if ($data['password'] != $data['confirmPassword']) {
            return [
                'canUserBeCreated' => false,
                'message' => 'Password and confirm password must match'
            ];
        }

        return [
            'canUserBeCreated' => true,
            'message' => 'User can be created'
        ];
    }
}
