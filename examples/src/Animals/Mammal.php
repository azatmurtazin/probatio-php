<?php

declare(strict_types=1);

namespace ProbatioExamples\Animals;

abstract class Mammal extends Animal
{
    public function getCovering(): string
    {
        return 'fur';
    }
}
