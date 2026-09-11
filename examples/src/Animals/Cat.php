<?php

declare(strict_types=1);

namespace Probatio\Examples\Animals;

class Cat extends Mammal
{
    public function makeSound(): string
    {
        return 'Meow!';
    }
}
