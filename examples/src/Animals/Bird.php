<?php

declare(strict_types=1);

namespace Probatio\Examples\Animals;

abstract class Bird extends Animal
{
    public function getCovering(): string
    {
        return 'feathers';
    }
}
