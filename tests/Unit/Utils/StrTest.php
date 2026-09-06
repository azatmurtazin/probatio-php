<?php

declare(strict_types=1);
use Probatio\Utils\Str;

test('startsWith', function () {
    $s1 = 'hello world';
    $s2 = 'hello';
    $s3 = 'world';

    expect(Str::startsWith($s1, $s2))->toBeTrue();
    expect(Str::startsWith($s1, $s3))->toBeFalse();
});
