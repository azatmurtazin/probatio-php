<?php

declare(strict_types=1);

require_once __DIR__ . "/../tests.php";

use ExampleApp\Greeter;

describe("tests of Greeter", function() {
    beforeAll(function() {
        $this->set("greeter", new Greeter());
    });

    afterAll(function() {
        $this->unset("greeter");
    });

    test("test John", function() {
        /** @var Greeter */
        $service = $this->get("greeter");

        $name = "John";

        $expected = "Hello, John!";
        $actual = $service->greet($name);

        $this->assertEq($expected, $actual);
    });

    test("test anon", function() {
        /** @var Greeter */
        $service = $this->get("greeter");

        $expected = "Hello, world!";
        $actual = $service->greet();

        $this->assertEq($expected, $actual);
    });

    test("test Sam", function() {
        /** @var Greeter */
        $service = $this->get("greeter");

        $name = "Sam";

        $expected = "Hi, Sam";
        $actual = $service->greet($name);

        $this->assertEq($expected, $actual);
    });
});
