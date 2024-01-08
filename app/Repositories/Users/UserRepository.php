<?php

namespace App\Repositories\Users;

use App\Interfaces\Users\UserRepositoryInterface;
use App\Models\Preference;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function find(string $infoToCompare, string $field)
    {
        return User::where($field, $infoToCompare)->first();
    }
    public function store(array $data)
    {
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];

        $user->save();

        $user->preferences()->create();
    }

    public function findByID(int $id)
    {
        return User::find($id)->load('preferences');
    }

    public function update(User $user, int $id, array $data)
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $user->preferences()->update($data['preferences']);

        return $user;
    }
}
