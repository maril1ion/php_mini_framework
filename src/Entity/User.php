<?php
declare(strict_types=1);
namespace App\Entity;
final class User
{
	public function __construct(
		private readonly ?int $id,
		private readonly string $name,
		private readonly string $email,
	) {}
}
