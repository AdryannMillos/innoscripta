<?php

namespace App\Interfaces\Users;

interface UserRepositoryInterface
{
    public function find(string $infoToCompare, string $field);

    public function store(array $data);
}
