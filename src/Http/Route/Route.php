<?php

declare(strict_types=1);

namespace App\Http\Route;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Route
{
  public function __construct(
    public readonly string $path,
    public readonly array $methods = ['GET']
  ) {}
}
