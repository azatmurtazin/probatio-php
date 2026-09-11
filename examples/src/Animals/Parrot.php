<?php

declare(strict_types=1);

namespace ProbatioExamples\Animals;

class Parrot extends Bird implements Flyable
{
    public function makeSound(): string
    {
        return 'Polly wants a cracker!';
    }

    public function fly(): string
    {
        return "{$this->name} is flying around the room.";
    }
}
