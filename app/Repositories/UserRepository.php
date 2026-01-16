<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function getActive(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()->where('active', true);

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    

    // Add other centralized queries as needed
}