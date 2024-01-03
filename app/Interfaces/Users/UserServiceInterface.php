<?php

namespace App\Interfaces\Users;

use App\Models\User;

interface UserServiceInterface
{
    public function create(array $data);

    public function validateUserCreation(array $data);

    public function findById(int $id);

    public function updateUser(User $user, int $id, array $data);
}
