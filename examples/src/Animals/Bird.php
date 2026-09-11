<?php

declare(strict_types=1);

namespace ProbatioExamples\Animals;

abstract class Bird extends Animal
{
    public function getCovering(): string
    {
        return 'feathers';
    }
}
