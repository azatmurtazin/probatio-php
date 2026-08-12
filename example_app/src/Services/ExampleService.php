<?php

declare(strict_types=1);

namespace ExampleApp\Services;

class ExampleService
{
    public function __construct()
    {
        echo "# run constructor of ExampleService\n";
    }

    public function __destruct()
    {
        echo "# run destructor of ExampleService\n";
    }

    public function greet(string $name): string
    {
        return "Hello, $name!";
    }
}
