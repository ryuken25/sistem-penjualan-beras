<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'full_name',
        'username',
        'password_hash',
        'profile_photo',
        'role',
        'is_active',
    ];

    public function getAllUsers(): array
    {
        return $this->orderBy('role', 'ASC')
            ->orderBy('full_name', 'ASC')
            ->findAll();
    }

    public function countActiveAdmins(?int $excludeId = null): int
    {
        $builder = $this->builder();
        $builder->where('role', 'admin')
            ->where('is_active', 1)
            ->where('deleted_at', null);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return (int) $builder->countAllResults();
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $builder = $this->builder();
        $builder->where('username', $username);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function getActiveUsers(): array
    {
        return $this->select('id, full_name, username, role')
            ->where('is_active', 1)
            ->orderBy('full_name', 'ASC')
            ->findAll();
    }
}
