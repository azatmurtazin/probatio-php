<?php

declare(strict_types=1);

namespace Probatio\Examples;

class Greeter
{
    public function greet(string $name = 'world'): string
    {
        return "Hello, $name!";
    }

    public function printGreeting(string $name = 'world')
    {
        echo $this->greet($name) . "\n";
    }
}
