<?php

declare(strict_types=1);

namespace ProbatioExamples\Animals;

class Turtle extends Reptile implements Swimmable
{
    public function makeSound(): string
    {
        return 'Hiss...';
    }

    public function swim(): string
    {
        return "{$this->name} is swimming in the ocean.";
    }
}
