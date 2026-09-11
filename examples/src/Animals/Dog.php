<?php

declare(strict_types=1);

namespace Probatio\Examples\Animals;

class Dog extends Mammal
{
    public function makeSound(): string
    {
        return 'Woof!';
    }
}
