<?php

declare(strict_types=1);
namespace App\Repository;
use App\Entity\User;
interface UserRepositoryInterface
{
	public function findById(int $id): ?User;
	public function findByName(string $name): ?User;
	public function findByEmail(string $email): ?User;
	public function findAll(): array;
	public function save(User $user): void;
	public function delete(int $id): void;
}
