<?php

declare(strict_types=1);

namespace ExampleApp;

class Greeter
{
    public function greet(string $name = "world"): string
    {
        return "Hello, $name!";
    }
}
