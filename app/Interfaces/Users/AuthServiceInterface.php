<?php

namespace App\Interfaces\Users;

interface AuthServiceInterface
{
    public function canUserLogin(array $data);

    public function userLogin(array $data);
}
