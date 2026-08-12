<?php

declare(strict_types=1);
require_once __DIR__."/../tests.php";

use ExampleApp\Services\ExampleService;

$ts = TestSuite::getInstance();

$ts->register("tests of ExampleService", function(TestCase $tc) {
    $tc->before(function(TestCase $tc) {
        $tc->set("service", new ExampleService());
    });

    $tc->after(function(TestCase $tc) {
        $tc->unset("service");
    });

    $tc->test("test John", function(TestCase $tc) {
        /** @var ExampleService */
        $service = $tc->get("service");

        $name = "John";

        $expected = "Hello, John!";
        $actual = $service->greet($name);

        $tc->assertEq($expected, $actual);
    });

    $tc->test("test anon", function(TestCase $tc) {
        /** @var ExampleService */
        $service = $tc->get("service");

        $expected = "Hello, world!";
        $actual = $service->greet();

        $tc->assertEq($expected, $actual);
    });

    $tc->test("test Sam", function(TestCase $tc) {
        /** @var ExampleService */
        $service = $tc->get("service");

        $name = "Sam";

        $expected = "Hi, Sam";
        $actual = $service->greet($name);

        $tc->assertEq($expected, $actual);
    });
})->maybeRunOneTest();
