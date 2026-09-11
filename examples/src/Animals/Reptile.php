<?php

declare(strict_types=1);

namespace ProbatioExamples\Animals;

abstract class Reptile extends Animal
{
    public function getCovering(): string
    {
        return 'scales or shell';
    }
}
