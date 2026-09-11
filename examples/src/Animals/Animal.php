<?php

declare(strict_types=1);

namespace ProbatioExamples\Animals;

abstract class Animal
{
    /** @var string */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function makeSound(): string;
}
