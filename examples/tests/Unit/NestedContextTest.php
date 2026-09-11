<?php

declare(strict_types=1);

use ProbatioExamples\Animals;

describe(Animals\Animal::class, function () {
    let('klass', function () {
        return Animals\Animal::class;
    });

    test('Animal class reflection', function () {
        $klass = $this->get('klass');
        expect($klass)->toBe(Animals\Animal::class);

        $ref = new \ReflectionClass($klass);
        expect($ref->isAbstract())->toBeTrue();
        expect($ref->getName())->toBe(Animals\Animal::class);
    });

    describe(Animals\Mammal::class, function () {
        let('klass', function () {
            return Animals\Mammal::class;
        });

        test('Mammal class reflection', function () {
            $klass = $this->get('klass');
            expect($klass)->toBe(Animals\Mammal::class);
        });

        describe(Animals\Cat::class, function () {
            set('klass', function () {
                return Animals\Cat::class;
            });

            test('Tom Cat', function () {
                $klass = $this->get('klass');
                $tom = new $klass('Tom');
                expect($tom->makeSound())->toBe('Meow!');
            });
        });
    });

    describe(Animals\Bird::class, function () {
        let('klass', function () {
            return Animals\Bird::class;
        });

        test('Bird class reflection', function () {
            $klass = $this->get('klass');
            expect($klass)->toBe(Animals\Bird::class);
        });

        describe(Animals\Duck::class, function () {
            set('klass', function () {
                return Animals\Duck::class;
            });

            test('Donald Duck', function () {
                $klass = $this->get('klass');
                /** @var Animals\Duck */
                $donald = new $klass('Donald');
                expect($donald->makeSound())->toBe('Quack!');
                expect($donald->swim())->toBe('Donald is paddling in the water.');
            });
        });
    });
});
