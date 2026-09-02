<?php

test('magic✨', function () {
    expect(join(["\xF0\x9F", "\xA6\x84"]))
        ->toBe('🦄');
});
