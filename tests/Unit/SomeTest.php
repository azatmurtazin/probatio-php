<?php

test('hello world', function () {
    expect(join(['hello', 'world'], ' '))->toBe('hello world');
});
