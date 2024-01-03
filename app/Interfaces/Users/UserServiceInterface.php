<?php

namespace App\Interfaces\Users;

interface UserServiceInterface
{
    public function create(array $data);

    public function validateUserCreation(array $data);
}
