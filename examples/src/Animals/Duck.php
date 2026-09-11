<?php

declare(strict_types=1);

namespace Probatio\Examples\Animals;

class Duck extends Bird implements Flyable, Swimmable
{
    public function makeSound(): string
    {
        return 'Quack!';
    }

    public function fly(): string
    {
        return "{$this->name} is flying over the pond.";
    }

    public function swim(): string
    {
        return "{$this->name} is paddling in the water.";
    }
}
