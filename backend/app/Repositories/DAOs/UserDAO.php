<?php

namespace App\Repositories\DAOs;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserDAO implements UserRepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function all(): LengthAwarePaginator
    {
        return $this->model
            ->with('pessoa')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function findById(int $id): ?User
    {
        try {
            return $this->model
                ->with('pessoa')
                ->where('id', $id)
                ->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model
            ->where('email', $email)
            ->first();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->findById($id);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado');
        }

        $user->update($data);
        return $user->fresh();
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado');
        }

        return $user->delete();
    }

    public function findByRole(string $role): LengthAwarePaginator
    {
        return $this->model
            ->whereHas('pessoa', function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->with('pessoa')
            ->orderBy('name')
            ->paginate(15);
    }
}