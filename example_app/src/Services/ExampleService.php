<?php

declare(strict_types=1);

namespace ExampleApp\Services;

class ExampleService
{
    public function greet(string $name = "world"): string
    {
        return "Hello, $name!";
    }
}
