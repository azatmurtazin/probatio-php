<?php

declare(strict_types=1);

use Probatio\Examples\Greeter;
use Probatio\TestCase;

describe('tests of Greeter', function () {
    beforeAll(function () {
        $this->set('greeter', new Greeter());
    });

    afterAll(function () {
        $this->unset('greeter');
    });

    test('with John', function () {
        /** @var Greeter */
        $service = $this->get('greeter');

        $name = 'John';

        $expected = 'Hello, John!';
        $actual = $service->greet($name);

        expect($actual)->toBe($expected);
    });

    test('with anon', function () {
        /** @var Greeter */
        $service = $this->get('greeter');

        $expected = 'Hello, world!';
        $actual = $service->greet();

        /** @var TestCase */
        $tc = $this;
        $tc->assertEq($expected, $actual);
    });
});
