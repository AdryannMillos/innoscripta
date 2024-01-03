<?php

namespace App\Interfaces\Users;

use App\Models\User;

interface UserRepositoryInterface
{
    public function find(string $infoToCompare, string $field);

    public function store(array $data);

    public function findByID(int $id);

    public function update(User $user, int $id, array $data);
}
