<?php

declare(strict_types=1);

namespace ProbatioExamples\Animals;

class Rabbit extends Mammal
{
    public function makeSound(): string
    {
        return 'Thump!';
    }
}
