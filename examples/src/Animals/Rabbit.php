<?php

declare(strict_types=1);

namespace Probatio\Examples\Animals;

class Rabbit extends Mammal
{
    public function makeSound(): string
    {
        return 'Thump!';
    }
}
