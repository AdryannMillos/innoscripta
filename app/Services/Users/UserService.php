<?php

namespace App\Services\Users;

use App\Interfaces\Users\UserServiceInterface;
use App\Interfaces\Users\UserRepositoryInterface;
use App\Models\User;
use Exception;

class UserService implements UserServiceInterface
{
    protected $userRepositoryInterface;

    public function __construct(
        UserRepositoryInterface $userRepositoryInterface
    ) {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    public function create(array $data)
    {
        $data['password'] = bcrypt($data['password']);

        return $this->userRepositoryInterface->store($data);
    }

    public function validateUserCreation(array $data)
    {
        $findEmail = $this->userRepositoryInterface->find($data['email'], 'email');

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

    public function findById(int $id)
    {
        return $this->userRepositoryInterface->findByID($id);
    }

    public function updateUser(User $user, int $id, array $data)
    {
        return $this->userRepositoryInterface->update($user, $id, $data);
    }
}
